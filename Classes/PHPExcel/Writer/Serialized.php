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
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Serialized
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Serialized implements PHPExcel_Writer_IWriter
{
	/**
	 * Private PHPExcel
	 *
	 * @var PHPExcel
	 */
	private $_spreadSheet;

    /**
     * Create a new PHPExcel_Writer_Serialized
     *
	 * @param 	PHPExcel	$pPHPExcel
     */
    public function __construct(PHPExcel $pPHPExcel = null)
    {
    	// Assign PHPExcel
		$this->setPHPExcel($pPHPExcel);
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
			// Garbage collect
			$this->_spreadSheet->garbageCollect();

			// Garbage collect...
			foreach ($this->_spreadSheet->getAllSheets() as $sheet) {
        		$sheet->garbageCollect();
			}

			// Create new ZIP file and open it for writing
			$objZip = new ZipArchive();

			// Try opening the ZIP file
			if ($objZip->open($pFilename, ZIPARCHIVE::OVERWRITE) !== true) {
				if ($objZip->open($pFilename, ZIPARCHIVE::CREATE) !== true) {
					throw new Exception("Could not open " . $pFilename . " for writing.");
				}
			}

			// Add media
			$sheetCount = $this->_spreadSheet->getSheetCount();
			for ($i = 0; $i < $sheetCount; ++$i) {
				for ($j = 0; $j < $this->_spreadSheet->getSheet($i)->getDrawingCollection()->count(); ++$j) {
					if ($this->_spreadSheet->getSheet($i)->getDrawingCollection()->offsetGet($j) instanceof PHPExcel_Worksheet_BaseDrawing) {
						$imgTemp = $this->_spreadSheet->getSheet($i)->getDrawingCollection()->offsetGet($j);
						$objZip->addFromString('media/' . $imgTemp->getFilename(), file_get_contents($imgTemp->getPath()));
					}
				}
			}

			// Add phpexcel.xml to the document, which represents a PHP serialized PHPExcel object
			$objZip->addFromString('phpexcel.xml', $this->_writeSerialized($this->_spreadSheet, $pFilename));

			// Close file
			if ($objZip->close() === false) {
				throw new Exception("Could not close zip file $pFilename.");
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
	 * Get PHPExcel object
	 *
	 * @param 	PHPExcel 	$pPHPExcel	PHPExcel object
	 * @throws	Exception
	 * @return PHPExcel_Writer_Serialized
	 */
	public function setPHPExcel(PHPExcel $pPHPExcel = null) {
		$this->_spreadSheet = $pPHPExcel;
		return $this;
	}

	/**
	 * Serialize PHPExcel object to XML
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @param 	string		$pFilename
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	private function _writeSerialized(PHPExcel $pPHPExcel = null, $pFilename = '')
	{
		// Clone $pPHPExcel
		$pPHPExcel = clone $pPHPExcel;

		// Update media links
		$sheetCount = $pPHPExcel->getSheetCount();
		for ($i = 0; $i < $sheetCount; ++$i) {
			for ($j = 0; $j < $pPHPExcel->getSheet($i)->getDrawingCollection()->count(); ++$j) {
				if ($pPHPExcel->getSheet($i)->getDrawingCollection()->offsetGet($j) instanceof PHPExcel_Worksheet_BaseDrawing) {
					$imgTemp =& $pPHPExcel->getSheet($i)->getDrawingCollection()->offsetGet($j);
					$imgTemp->setPath('zip://' . $pFilename . '#media/' . $imgTemp->getFilename(), false);
				}
			}
		}

		// Create XML writer
		$objWriter = new xmlWriter();
		$objWriter->openMemory();
		$objWriter->setIndent(true);

		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');

		// PHPExcel
		$objWriter->startElement('PHPExcel');
		$objWriter->writeAttribute('version', '##VERSION##');

			// Comment
			$objWriter->writeComment('This file has been generated using PHPExcel v##VERSION## (http://www.codeplex.com/PHPExcel). It contains a base64 encoded serialized version of the PHPExcel internal object.');

			// Data
			$objWriter->startElement('data');
				$objWriter->writeCData( base64_encode(serialize($pPHPExcel)) );
			$objWriter->endElement();

		$objWriter->endElement();

		// Return
		return $objWriter->outputMemory(true);
	}
}
