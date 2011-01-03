<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel5
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel5 implements PHPExcel_Writer_IWriter
{
	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas	= true;

	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	private $_phpExcel;

	/**
	 * The BIFF version of the written Excel file, BIFF5 = 0x0500, BIFF8 = 0x0600
	 *
	 * @var integer
	 */
	private $_BIFF_version	= 0x0600;

	/**
	 * Total number of shared strings in workbook
	 *
	 * @var int
	 */
	private $_str_total		= 0;

	/**
	 * Number of unique shared strings in workbook
	 *
	 * @var int
	 */
	private $_str_unique	= 0;

	/**
	 * Array of unique shared strings in workbook
	 *
	 * @var array
	 */
	private $_str_table		= array();

	/**
	 * Color cache. Mapping between RGB value and color index.
	 *
	 * @var array
	 */
	private $_colors;

	/**
	 * Formula parser
	 *
	 * @var PHPExcel_Writer_Excel5_Parser
	 */
	private $_parser;

	/**
	 * Identifier clusters for drawings. Used in MSODRAWINGGROUP record.
	 *
	 * @var array
	 */
	private $_IDCLs;


	/**
	 * Create a new PHPExcel_Writer_Excel5
	 *
	 * @param	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel		= $phpExcel;

		$this->_parser			= new PHPExcel_Writer_Excel5_Parser($this->_BIFF_version);
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param	string		$pFileName
	 * @throws	Exception
	 */
	public function save($pFilename = null) {

		// garbage collect
		$this->_phpExcel->garbageCollect();

		$saveDebugLog = PHPExcel_Calculation::getInstance()->writeDebugLog;
		PHPExcel_Calculation::getInstance()->writeDebugLog = false;
		$saveDateReturnType = PHPExcel_Calculation_Functions::getReturnDateType();
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

		// initialize colors array
		$this->_colors          = array();

		// Initialise workbook writer
		$this->_writerWorkbook = new PHPExcel_Writer_Excel5_Workbook($this->_phpExcel, $this->_BIFF_version,
					$this->_str_total, $this->_str_unique, $this->_str_table, $this->_colors, $this->_parser);

		// Initialise worksheet writers
		$countSheets = $this->_phpExcel->getSheetCount();
		for ($i = 0; $i < $countSheets; ++$i) {
			$this->_writerWorksheets[$i] = new PHPExcel_Writer_Excel5_Worksheet($this->_BIFF_version,
									   $this->_str_total, $this->_str_unique,
									   $this->_str_table, $this->_colors,
									   $this->_parser,
									   $this->_preCalculateFormulas,
									   $this->_phpExcel->getSheet($i));
		}

		// build Escher objects. Escher objects for workbooks needs to be build before Escher object for workbook.
		$this->_buildWorksheetEschers();
		$this->_buildWorkbookEscher();

		// add 15 identical cell style Xfs
		// for now, we use the first cellXf instead of cellStyleXf
		$cellXfCollection = $this->_phpExcel->getCellXfCollection();
		for ($i = 0; $i < 15; ++$i) {
			$this->_writerWorkbook->addXfWriter($cellXfCollection[0], true);
		}

		// add all the cell Xfs
		foreach ($this->_phpExcel->getCellXfCollection() as $style) {
			$this->_writerWorkbook->addXfWriter($style, false);
		}

		// initialize OLE file
		$workbookStreamName = ($this->_BIFF_version == 0x0600) ? 'Workbook' : 'Book';
		$OLE = new PHPExcel_Shared_OLE_PPS_File(PHPExcel_Shared_OLE::Asc2Ucs($workbookStreamName));

		// Write the worksheet streams before the global workbook stream,
		// because the byte sizes of these are needed in the global workbook stream
		$worksheetSizes = array();
		for ($i = 0; $i < $countSheets; ++$i) {
			$this->_writerWorksheets[$i]->close();
			$worksheetSizes[] = $this->_writerWorksheets[$i]->_datasize;
		}

		// add binary data for global workbook stream
		$OLE->append( $this->_writerWorkbook->writeWorkbook($worksheetSizes) );

		// add binary data for sheet streams
		for ($i = 0; $i < $countSheets; ++$i) {
			$OLE->append($this->_writerWorksheets[$i]->getData());
		}

		$root = new PHPExcel_Shared_OLE_PPS_Root(time(), time(), array($OLE));
		// save the OLE file
		$res = $root->save($pFilename);

		PHPExcel_Calculation_Functions::setReturnDateType($saveDateReturnType);
		PHPExcel_Calculation::getInstance()->writeDebugLog = $saveDebugLog;
	}

	/**
	 * Set temporary storage directory
	 *
	 * @deprecated
	 * @param	string	$pValue		Temporary storage directory
	 * @throws	Exception	Exception when directory does not exist
	 * @return PHPExcel_Writer_Excel5
	 */
	public function setTempDir($pValue = '') {
		return $this;
	}

	/**
	 * Get Pre-Calculate Formulas
	 *
	 * @return boolean
	 */
	public function getPreCalculateFormulas() {
		return $this->_preCalculateFormulas;
	}

	/**
	 * Set Pre-Calculate Formulas
	 *
	 * @param boolean $pValue	Pre-Calculate Formulas?
	 */
	public function setPreCalculateFormulas($pValue = true) {
		$this->_preCalculateFormulas = $pValue;
	}

	private function _buildWorksheetEschers()
	{
		// 1-based index to BstoreContainer
		$blipIndex = 0;

		foreach ($this->_phpExcel->getAllsheets() as $sheet) {
			// sheet index
			$sheetIndex = $sheet->getParent()->getIndex($sheet);

			$escher = null;

			// check if there are any shapes for this sheet
			if (count($sheet->getDrawingCollection()) == 0) {
				continue;
			}

			// create intermediate Escher object
			$escher = new PHPExcel_Shared_Escher();

			// dgContainer
			$dgContainer = new PHPExcel_Shared_Escher_DgContainer();

			// set the drawing index (we use sheet index + 1)
			$dgId = $sheet->getParent()->getIndex($sheet) + 1;
			$dgContainer->setDgId($dgId);
			$escher->setDgContainer($dgContainer);

			// spgrContainer
			$spgrContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer();
			$dgContainer->setSpgrContainer($spgrContainer);

			// add one shape which is the group shape
			$spContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer();
			$spContainer->setSpgr(true);
			$spContainer->setSpType(0);
			$spContainer->setSpId(($sheet->getParent()->getIndex($sheet) + 1) << 10);
			$spgrContainer->addChild($spContainer);

			// add the shapes

			$countShapes[$sheetIndex] = 0; // count number of shapes (minus group shape), in sheet

			foreach ($sheet->getDrawingCollection() as $drawing) {
				++$blipIndex;

				++$countShapes[$sheetIndex];

				// add the shape
				$spContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer();

				// set the shape type
				$spContainer->setSpType(0x004B);

				// set the shape index (we combine 1-based sheet index and $countShapes to create unique shape index)
				$reducedSpId = $countShapes[$sheetIndex];
				$spId = $reducedSpId
					| ($sheet->getParent()->getIndex($sheet) + 1) << 10;
				$spContainer->setSpId($spId);

				// keep track of last reducedSpId
				$lastReducedSpId = $reducedSpId;

				// keep track of last spId
				$lastSpId = $spId;

				// set the BLIP index
				$spContainer->setOPT(0x4104, $blipIndex);

				// set coordinates and offsets, client anchor
				$coordinates = $drawing->getCoordinates();
				$offsetX = $drawing->getOffsetX();
				$offsetY = $drawing->getOffsetY();
				$width = $drawing->getWidth();
				$height = $drawing->getHeight();

				$twoAnchor = PHPExcel_Shared_Excel5::oneAnchor2twoAnchor($sheet, $coordinates, $offsetX, $offsetY, $width, $height);

				$spContainer->setStartCoordinates($twoAnchor['startCoordinates']);
				$spContainer->setStartOffsetX($twoAnchor['startOffsetX']);
				$spContainer->setStartOffsetY($twoAnchor['startOffsetY']);
				$spContainer->setEndCoordinates($twoAnchor['endCoordinates']);
				$spContainer->setEndOffsetX($twoAnchor['endOffsetX']);
				$spContainer->setEndOffsetY($twoAnchor['endOffsetY']);

				$spgrContainer->addChild($spContainer);
			}

			// identifier clusters, used for workbook Escher object
			$this->_IDCLs[$dgId] = $lastReducedSpId;

			// set last shape index
			$dgContainer->setLastSpId($lastSpId);

			// set the Escher object
			$this->_writerWorksheets[$sheetIndex]->setEscher($escher);
		}
	}

	/**
	 * Build the Escher object corresponding to the MSODRAWINGGROUP record
	 */
	private function _buildWorkbookEscher()
	{
		$escher = null;

		// any drawings in this workbook?
		$found = false;
		foreach ($this->_phpExcel->getAllSheets() as $sheet) {
			if (count($sheet->getDrawingCollection()) > 0) {
				$found = true;
			}
		}

		// nothing to do if there are no drawings
		if (!$found) {
			return;
		}

		// if we reach here, then there are drawings in the workbook
		$escher = new PHPExcel_Shared_Escher();

		// dggContainer
		$dggContainer = new PHPExcel_Shared_Escher_DggContainer();
		$escher->setDggContainer($dggContainer);

		// set IDCLs (identifier clusters)
		$dggContainer->setIDCLs($this->_IDCLs);

		// this loop is for determining maximum shape identifier of all drawing
		$spIdMax = 0;
		$totalCountShapes = 0;
		$countDrawings = 0;

		foreach ($this->_phpExcel->getAllsheets() as $sheet) {
			$sheetCountShapes = 0; // count number of shapes (minus group shape), in sheet

			if (count($sheet->getDrawingCollection()) > 0) {
				++$countDrawings;

				foreach ($sheet->getDrawingCollection() as $drawing) {
					++$sheetCountShapes;
					++$totalCountShapes;

					$spId = $sheetCountShapes
						| ($this->_phpExcel->getIndex($sheet) + 1) << 10;
					$spIdMax = max($spId, $spIdMax);
				}
			}
		}

		$dggContainer->setSpIdMax($spIdMax + 1);
		$dggContainer->setCDgSaved($countDrawings);
		$dggContainer->setCSpSaved($totalCountShapes + $countDrawings); // total number of shapes incl. one group shapes per drawing

		// bstoreContainer
		$bstoreContainer = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer();
		$dggContainer->setBstoreContainer($bstoreContainer);

		// the BSE's (all the images)
		foreach ($this->_phpExcel->getAllsheets() as $sheet) {
			foreach ($sheet->getDrawingCollection() as $drawing) {
				if ($drawing instanceof PHPExcel_Worksheet_Drawing) {

					$filename = $drawing->getPath();

					list($imagesx, $imagesy, $imageFormat) = getimagesize($filename);

					switch ($imageFormat) {

					case 1: // GIF, not supported by BIFF8, we convert to PNG
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
						ob_start();
						imagepng(imagecreatefromgif($filename));
						$blipData = ob_get_contents();
						ob_end_clean();
						break;

					case 2: // JPEG
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG;
						$blipData = file_get_contents($filename);
						break;

					case 3: // PNG
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
						$blipData = file_get_contents($filename);
						break;

					case 6: // Windows DIB (BMP), we convert to PNG
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
						ob_start();
						imagepng(PHPExcel_Shared_Drawing::imagecreatefrombmp($filename));
						$blipData = ob_get_contents();
						ob_end_clean();
						break;

					default: continue 2;

					}

					$blip = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip();
					$blip->setData($blipData);

					$BSE = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE();
					$BSE->setBlipType($blipType);
					$BSE->setBlip($blip);

					$bstoreContainer->addBSE($BSE);

				} else if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {

					switch ($drawing->getRenderingFunction()) {

					case PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG:
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG;
						$renderingFunction = 'imagejpeg';
						break;

					case PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF:
					case PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG:
					case PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT:
						$blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
						$renderingFunction = 'imagepng';
						break;

					}

					ob_start();
					call_user_func($renderingFunction, $drawing->getImageResource());
					$blipData = ob_get_contents();
					ob_end_clean();

					$blip = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip();
					$blip->setData($blipData);

					$BSE = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE();
					$BSE->setBlipType($blipType);
					$BSE->setBlip($blip);

					$bstoreContainer->addBSE($BSE);
				}
			}
		}

		// Set the Escher object
		$this->_writerWorkbook->setEscher($escher);
	}

}
