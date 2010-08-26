<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel2007
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007 implements PHPExcel_Writer_IWriter
{
	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas = true;

	/**
	 * Office2003 compatibility
	 *
	 * @var boolean
	 */
	private $_office2003compatibility = false;

	/**
	 * Private writer parts
	 *
	 * @var PHPExcel_Writer_Excel2007_WriterPart[]
	 */
	private $_writerParts;

	/**
	 * Private PHPExcel
	 *
	 * @var PHPExcel
	 */
	private $_spreadSheet;

	/**
	 * Private string table
	 *
	 * @var string[]
	 */
	private $_stringTable;

	/**
	 * Private unique PHPExcel_Style_Conditional HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_stylesConditionalHashTable;

	/**
	 * Private unique PHPExcel_Style_Fill HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_fillHashTable;

	/**
	 * Private unique PHPExcel_Style_Font HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_fontHashTable;

	/**
	 * Private unique PHPExcel_Style_Borders HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_bordersHashTable ;

	/**
	 * Private unique PHPExcel_Style_NumberFormat HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_numFmtHashTable;

	/**
	 * Private unique PHPExcel_Worksheet_BaseDrawing HashTable
	 *
	 * @var PHPExcel_HashTable
	 */
	private $_drawingHashTable;

	/**
	 * Use disk caching where possible?
	 *
	 * @var boolean
	 */
	private $_useDiskCaching = false;

	/**
	 * Disk caching directory
	 *
	 * @var string
	 */
	private $_diskCachingDirectory;

    /**
     * Create a new PHPExcel_Writer_Excel2007
     *
	 * @param 	PHPExcel	$pPHPExcel
     */
    public function __construct(PHPExcel $pPHPExcel = null)
    {
    	// Assign PHPExcel
		$this->setPHPExcel($pPHPExcel);

		// Set up disk caching location
		$this->_diskCachingDirectory = './';

    	// Initialise writer parts
    	$this->_writerParts['stringtable']		= new PHPExcel_Writer_Excel2007_StringTable();
		$this->_writerParts['contenttypes'] 	= new PHPExcel_Writer_Excel2007_ContentTypes();
		$this->_writerParts['docprops'] 		= new PHPExcel_Writer_Excel2007_DocProps();
		$this->_writerParts['rels'] 			= new PHPExcel_Writer_Excel2007_Rels();
		$this->_writerParts['theme'] 			= new PHPExcel_Writer_Excel2007_Theme();
		$this->_writerParts['style'] 			= new PHPExcel_Writer_Excel2007_Style();
		$this->_writerParts['workbook'] 		= new PHPExcel_Writer_Excel2007_Workbook();
		$this->_writerParts['worksheet'] 		= new PHPExcel_Writer_Excel2007_Worksheet();
		$this->_writerParts['drawing'] 			= new PHPExcel_Writer_Excel2007_Drawing();
		$this->_writerParts['comments'] 		= new PHPExcel_Writer_Excel2007_Comments();

		// Assign parent IWriter
		foreach ($this->_writerParts as $writer) {
			$writer->setParentWriter($this);
		}

		// Set HashTable variables
		$this->_stringTable					= array();
		$this->_stylesConditionalHashTable 	= new PHPExcel_HashTable();
		$this->_fillHashTable 				= new PHPExcel_HashTable();
		$this->_fontHashTable 				= new PHPExcel_HashTable();
		$this->_bordersHashTable 			= new PHPExcel_HashTable();
		$this->_numFmtHashTable 			= new PHPExcel_HashTable();
		$this->_drawingHashTable 			= new PHPExcel_HashTable();
    }

	/**
	 * Get writer part
	 *
	 * @param 	string 	$pPartName		Writer part name
	 * @return 	PHPExcel_Writer_Excel2007_WriterPart
	 */
	function getWriterPart($pPartName = '') {
		if ($pPartName != '' && isset($this->_writerParts[strtolower($pPartName)])) {
			return $this->_writerParts[strtolower($pPartName)];
		} else {
			return null;
		}
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param 	string 		$pFileName
	 * @throws 	Exception
	 */
	public function save($pFilename = null)
	{
		if (!is_null($this->_spreadSheet)) {
			// garbage collect
			$this->_spreadSheet->garbageCollect();

			// If $pFilename is php://output or php://stdout, make it a temporary file...
			$originalFilename = $pFilename;
			if (strtolower($pFilename) == 'php://output' || strtolower($pFilename) == 'php://stdout') {
				$pFilename = @tempnam('./', 'phpxltmp');
				if ($pFilename == '') {
					$pFilename = $originalFilename;
				}
			}

			$saveDebugLog = PHPExcel_Calculation::getInstance()->writeDebugLog;
			PHPExcel_Calculation::getInstance()->writeDebugLog = false;
			$saveDateReturnType = PHPExcel_Calculation_Functions::getReturnDateType();
			PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

			// Create string lookup table
			$this->_stringTable = array();
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); ++$i) {
				$this->_stringTable = $this->getWriterPart('StringTable')->createStringTable($this->_spreadSheet->getSheet($i), $this->_stringTable);
			}

			// Create styles dictionaries
			$this->_stylesConditionalHashTable->addFromSource( 	$this->getWriterPart('Style')->allConditionalStyles($this->_spreadSheet) 			);
			$this->_fillHashTable->addFromSource( 				$this->getWriterPart('Style')->allFills($this->_spreadSheet) 			);
			$this->_fontHashTable->addFromSource( 				$this->getWriterPart('Style')->allFonts($this->_spreadSheet) 			);
			$this->_bordersHashTable->addFromSource( 			$this->getWriterPart('Style')->allBorders($this->_spreadSheet) 			);
			$this->_numFmtHashTable->addFromSource( 			$this->getWriterPart('Style')->allNumberFormats($this->_spreadSheet) 	);

			// Create drawing dictionary
			$this->_drawingHashTable->addFromSource( 			$this->getWriterPart('Drawing')->allDrawings($this->_spreadSheet) 		);

			// Create new ZIP file and open it for writing
			$objZip = new ZipArchive();

			// Try opening the ZIP file
			if ($objZip->open($pFilename, ZIPARCHIVE::OVERWRITE) !== true) {
				if ($objZip->open($pFilename, ZIPARCHIVE::CREATE) !== true) {
					throw new Exception("Could not open " . $pFilename . " for writing.");
				}
			}

			// Add [Content_Types].xml to ZIP file
			$objZip->addFromString('[Content_Types].xml', 			$this->getWriterPart('ContentTypes')->writeContentTypes($this->_spreadSheet));

			// Add relationships to ZIP file
			$objZip->addFromString('_rels/.rels', 					$this->getWriterPart('Rels')->writeRelationships($this->_spreadSheet));
			$objZip->addFromString('xl/_rels/workbook.xml.rels', 	$this->getWriterPart('Rels')->writeWorkbookRelationships($this->_spreadSheet));

			// Add document properties to ZIP file
			$objZip->addFromString('docProps/app.xml', 				$this->getWriterPart('DocProps')->writeDocPropsApp($this->_spreadSheet));
			$objZip->addFromString('docProps/core.xml', 			$this->getWriterPart('DocProps')->writeDocPropsCore($this->_spreadSheet));

			// Add theme to ZIP file
			$objZip->addFromString('xl/theme/theme1.xml', 			$this->getWriterPart('Theme')->writeTheme($this->_spreadSheet));

			// Add string table to ZIP file
			$objZip->addFromString('xl/sharedStrings.xml', 			$this->getWriterPart('StringTable')->writeStringTable($this->_stringTable));

			// Add styles to ZIP file
			$objZip->addFromString('xl/styles.xml', 				$this->getWriterPart('Style')->writeStyles($this->_spreadSheet));

			// Add workbook to ZIP file
			$objZip->addFromString('xl/workbook.xml', 				$this->getWriterPart('Workbook')->writeWorkbook($this->_spreadSheet));

			// Add worksheets
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); ++$i) {
				$objZip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->getWriterPart('Worksheet')->writeWorksheet($this->_spreadSheet->getSheet($i), $this->_stringTable));
			}

			// Add worksheet relationships (drawings, ...)
			for ($i = 0; $i < $this->_spreadSheet->getSheetCount(); ++$i) {

				// Add relationships
				$objZip->addFromString('xl/worksheets/_rels/sheet' . ($i + 1) . '.xml.rels', 	$this->getWriterPart('Rels')->writeWorksheetRelationships($this->_spreadSheet->getSheet($i), ($i + 1)));

				// Add drawing relationship parts
				if ($this->_spreadSheet->getSheet($i)->getDrawingCollection()->count() > 0) {
					// Drawing relationships
					$objZip->addFromString('xl/drawings/_rels/drawing' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeDrawingRelationships($this->_spreadSheet->getSheet($i)));

					// Drawings
					$objZip->addFromString('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->_spreadSheet->getSheet($i)));
				}

				// Add comment relationship parts
				if (count($this->_spreadSheet->getSheet($i)->getComments()) > 0) {
					// VML Comments
					$objZip->addFromString('xl/drawings/vmlDrawing' . ($i + 1) . '.vml', $this->getWriterPart('Comments')->writeVMLComments($this->_spreadSheet->getSheet($i)));

					// Comments
					$objZip->addFromString('xl/comments' . ($i + 1) . '.xml', $this->getWriterPart('Comments')->writeComments($this->_spreadSheet->getSheet($i)));
				}

				// Add header/footer relationship parts
				if (count($this->_spreadSheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
					// VML Drawings
					$objZip->addFromString('xl/drawings/vmlDrawingHF' . ($i + 1) . '.vml', $this->getWriterPart('Drawing')->writeVMLHeaderFooterImages($this->_spreadSheet->getSheet($i)));

					// VML Drawing relationships
					$objZip->addFromString('xl/drawings/_rels/vmlDrawingHF' . ($i + 1) . '.vml.rels', $this->getWriterPart('Rels')->writeHeaderFooterDrawingRelationships($this->_spreadSheet->getSheet($i)));

					// Media
					foreach ($this->_spreadSheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
						$objZip->addFromString('xl/media/' . $image->getIndexedFilename(), file_get_contents($image->getPath()));
					}
				}
			}

			// Add media
			for ($i = 0; $i < $this->getDrawingHashTable()->count(); ++$i) {
				if ($this->getDrawingHashTable()->getByIndex($i) instanceof PHPExcel_Worksheet_Drawing) {
					$imageContents = null;
					$imagePath = $this->getDrawingHashTable()->getByIndex($i)->getPath();

					if (strpos($imagePath, 'zip://') !== false) {
						$imagePath = substr($imagePath, 6);
						$imagePathSplitted = explode('#', $imagePath);

						$imageZip = new ZipArchive();
						$imageZip->open($imagePathSplitted[0]);
						$imageContents = $imageZip->getFromName($imagePathSplitted[1]);
						$imageZip->close();
						unset($imageZip);
					} else {
						$imageContents = file_get_contents($imagePath);
					}

					$objZip->addFromString('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
				} else if ($this->getDrawingHashTable()->getByIndex($i) instanceof PHPExcel_Worksheet_MemoryDrawing) {
					ob_start();
					call_user_func(
						$this->getDrawingHashTable()->getByIndex($i)->getRenderingFunction(),
						$this->getDrawingHashTable()->getByIndex($i)->getImageResource()
					);
					$imageContents = ob_get_contents();
					ob_end_clean();

					$objZip->addFromString('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
				}
			}

			PHPExcel_Calculation_Functions::setReturnDateType($saveDateReturnType);
			PHPExcel_Calculation::getInstance()->writeDebugLog = $saveDebugLog;

			// Close file
			if ($objZip->close() === false) {
				throw new Exception("Could not close zip file $pFilename.");
			}

			// If a temporary file was used, copy it to the correct file stream
			if ($originalFilename != $pFilename) {
				if (copy($pFilename, $originalFilename) === false) {
					throw new Exception("Could not copy temporary zip file $pFilename to $originalFilename.");
				}
				@unlink($pFilename);
			}
		} else {
			throw new Exception("PHPExcel object unassigned.");
		}
	}

	/**
	 * Get PHPExcel object
	 *
	 * @return PHPExcel
	 * @throws Exception
	 */
	public function getPHPExcel() {
		if (!is_null($this->_spreadSheet)) {
			return $this->_spreadSheet;
		} else {
			throw new Exception("No PHPExcel assigned.");
		}
	}

	/**
	 * Set PHPExcel object
	 *
	 * @param 	PHPExcel 	$pPHPExcel	PHPExcel object
	 * @throws	Exception
	 * @return PHPExcel_Writer_Excel2007
	 */
	public function setPHPExcel(PHPExcel $pPHPExcel = null) {
		$this->_spreadSheet = $pPHPExcel;
		return $this;
	}

    /**
     * Get string table
     *
     * @return string[]
     */
    public function getStringTable() {
    	return $this->_stringTable;
    }

    /**
     * Get PHPExcel_Style_Conditional HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getStylesConditionalHashTable() {
    	return $this->_stylesConditionalHashTable;
    }

    /**
     * Get PHPExcel_Style_Fill HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFillHashTable() {
    	return $this->_fillHashTable;
    }

    /**
     * Get PHPExcel_Style_Font HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFontHashTable() {
    	return $this->_fontHashTable;
    }

    /**
     * Get PHPExcel_Style_Borders HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getBordersHashTable() {
    	return $this->_bordersHashTable;
    }

    /**
     * Get PHPExcel_Style_NumberFormat HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getNumFmtHashTable() {
    	return $this->_numFmtHashTable;
    }

    /**
     * Get PHPExcel_Worksheet_BaseDrawing HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getDrawingHashTable() {
    	return $this->_drawingHashTable;
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

    /**
     * Get Office2003 compatibility
     *
     * @return boolean
     */
    public function getOffice2003Compatibility() {
    	return $this->_office2003compatibility;
    }

    /**
     * Set Pre-Calculate Formulas
     *
     * @param boolean $pValue	Office2003 compatibility?
     * @return PHPExcel_Writer_Excel2007
     */
    public function setOffice2003Compatibility($pValue = false) {
    	$this->_office2003compatibility = $pValue;
    	return $this;
    }

	/**
	 * Get use disk caching where possible?
	 *
	 * @return boolean
	 */
	public function getUseDiskCaching() {
		return $this->_useDiskCaching;
	}

	/**
	 * Set use disk caching where possible?
	 *
	 * @param 	boolean 	$pValue
	 * @param	string		$pDirectory		Disk caching directory
	 * @throws	Exception	Exception when directory does not exist
	 * @return PHPExcel_Writer_Excel2007
	 */
	public function setUseDiskCaching($pValue = false, $pDirectory = null) {
		$this->_useDiskCaching = $pValue;

		if (!is_null($pDirectory)) {
    		if (is_dir($pDirectory)) {
    			$this->_diskCachingDirectory = $pDirectory;
    		} else {
    			throw new Exception("Directory does not exist: $pDirectory");
    		}
		}
		return $this;
	}

	/**
	 * Get disk caching directory
	 *
	 * @return string
	 */
	public function getDiskCachingDirectory() {
		return $this->_diskCachingDirectory;
	}
}
