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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	PHPExcel_Autoloader::Register();
	PHPExcel_Shared_ZipStreamWrapper::register();
	// check mbstring.func_overload
	if (ini_get('mbstring.func_overload') & 2) {
		throw new Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
	}
}

/**
 * PHPExcel_Reader_Gnumeric
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Gnumeric implements PHPExcel_Reader_IReader
{
	/**
	 * Read data only?
	 *
	 * @var boolean
	 */
	private $_readDataOnly = false;

	/**
	 * Restict which sheets should be loaded?
	 *
	 * @var array
	 */
	private $_loadSheetsOnly = null;

	/**
	 * Sheet index to read
	 *
	 * @var int
	 */
	private $_sheetIndex;

	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_styles = array();

	/**
	 * Shared Expressions
	 *
	 * @var array
	 */
	private $_expressions = array();

	private $_referenceHelper = null;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;


	/**
	 * Read data only?
	 *
	 * @return boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}

	/**
	 * Set read data only
	 *
	 * @param boolean $pValue
	 * @return PHPExcel_Reader_Gnumeric
	 */
	public function setReadDataOnly($pValue = false) {
		$this->_readDataOnly = $pValue;
		return $this;
	}

	/**
	 * Get which sheets to load
	 *
	 * @return mixed
	 */
	public function getLoadSheetsOnly()
	{
		return $this->_loadSheetsOnly;
	}

	/**
	 * Set which sheets to load
	 *
	 * @param mixed $value
	 * @return PHPExcel_Reader_Gnumeric
	 */
	public function setLoadSheetsOnly($value = null)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
		return $this;
	}

	/**
	 * Set all sheets to load
	 *
	 * @return PHPExcel_Reader_Gnumeric
	 */
	public function setLoadAllSheets()
	{
		$this->_loadSheetsOnly = null;
		return $this;
	}

	/**
	 * Read filter
	 *
	 * @return PHPExcel_Reader_IReadFilter
	 */
	public function getReadFilter() {
		return $this->_readFilter;
	}

	/**
	 * Set read filter
	 *
	 * @param PHPExcel_Reader_IReadFilter $pValue
	 * @return PHPExcel_Reader_Gnumeric
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
		return $this;
	}

	/**
	 * Create a new PHPExcel_Reader_Gnumeric
	 */
	public function __construct() {
		$this->_sheetIndex 	= 0;
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
		$this->_referenceHelper = PHPExcel_ReferenceHelper::getInstance();
	}

	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFileName
	 * @return 	boolean
	 */
	public function canRead($pFilename)
	{
		// Check if zip class exists
		if (!function_exists('gzread')) {
			return false;
		}

		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		return true;
	}

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Create new PHPExcel
		$objPHPExcel = new PHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objPHPExcel);
	}

	private static function identifyFixedStyleValue($styleList,&$styleAttributeValue) {
		$styleAttributeValue = strtolower($styleAttributeValue);
		foreach($styleList as $style) {
			if ($styleAttributeValue == strtolower($style)) {
				$styleAttributeValue = $style;
				return true;
			}
		}
		return false;
	}

	private function _gzfileGetContents($filename) {
		$file = @gzopen($filename, 'rb');
		if ($file !== false) {
			$data = '';
			while (!gzeof($file)) {
				$data .= gzread($file, 1024);
			}
			gzclose($file);
		}
		return $data;
	}

	/**
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$timezoneObj = new DateTimeZone('Europe/London');
		$GMT = new DateTimeZone('UTC');

		$gFileData = $this->_gzfileGetContents($pFilename);

//		echo '<pre>';
//		echo htmlentities($gFileData);
//		echo '</pre><hr />';
//
		$xml = simplexml_load_string($gFileData);
		$namespacesMeta = $xml->getNamespaces(true);

//		var_dump($namespacesMeta);
//
		$gnmXML = $xml->children($namespacesMeta['gnm']);

		$officeXML = $xml->children($namespacesMeta['office']);
	    $officeDocXML = $officeXML->{'document-meta'};
		$officeDocMetaXML = $officeDocXML->meta;

		$docProps = $objPHPExcel->getProperties();

		foreach($officeDocMetaXML as $officePropertyData) {

			$officePropertyDC = array();
			if (isset($namespacesMeta['dc'])) {
				$officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
			}
			foreach($officePropertyDC as $propertyName => $propertyValue) {
				$propertyValue = (string) $propertyValue;
				switch ($propertyName) {
					case 'title' :
							$docProps->setTitle(trim($propertyValue));
							break;
					case 'subject' :
							$docProps->setSubject(trim($propertyValue));
							break;
					case 'creator' :
							$docProps->setCreator(trim($propertyValue));
							break;
					case 'date' :
							$creationDate = strtotime(trim($propertyValue));
							$docProps->setCreated($creationDate);
							break;
					case 'description' :
							$docProps->setDescription(trim($propertyValue));
							break;
				}
			}
			$officePropertyMeta = array();
			if (isset($namespacesMeta['meta'])) {
				$officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
			}
			foreach($officePropertyMeta as $propertyName => $propertyValue) {
				$attributes = $propertyValue->attributes($namespacesMeta['meta']);
				$propertyValue = (string) $propertyValue;
				switch ($propertyName) {
					case 'keyword' :
							$docProps->setKeywords(trim($propertyValue));
							break;
					case 'initial-creator' :
							$docProps->setCreator(trim($propertyValue));
							break;
					case 'creation-date' :
							$creationDate = strtotime(trim($propertyValue));
							$docProps->setCreated($creationDate);
							break;
					case 'user-defined' :
							list(,$attrName) = explode(':',$attributes['name']);
							switch ($attrName) {
								case 'publisher' :
										$docProps->setCompany(trim($propertyValue));
										break;
								case 'category' :
										$docProps->setCategory(trim($propertyValue));
										break;
								case 'manager' :
										$docProps->setManager(trim($propertyValue));
										break;
							}
							break;
				}
			}
		}


		$worksheetID = 0;
		foreach($gnmXML->Sheets->Sheet as $sheet) {
			$worksheetName = (string) $sheet->Name;
//			echo '<b>Worksheet: ',$worksheetName,'</b><br />';
			if ((isset($this->_loadSheetsOnly)) && (!in_array($worksheetName, $this->_loadSheetsOnly))) {
				continue;
			}

			// Create new Worksheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($worksheetID);
			$objPHPExcel->getActiveSheet()->setTitle($worksheetName);

			foreach($sheet->Cells->Cell as $cell) {
				$cellAttributes = $cell->attributes();
				$row = (string) $cellAttributes->Row + 1;
				$column = PHPExcel_Cell::stringFromColumnIndex($cellAttributes->Col);
				$ValueType = $cellAttributes->ValueType;
				$ExprID = (string) $cellAttributes->ExprID;
//				echo 'Cell ',$column,$row,'<br />';
//				echo 'Type is ',$ValueType,'<br />';
//				echo 'Value is ',$cell,'<br />';
				$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
				if ($ExprID > '') {
					if (((string) $cell) > '') {

						$this->_expressions[$ExprID] = array( 'column'	=> $cellAttributes->Col,
															  'row'		=> $cellAttributes->Row,
															  'formula'	=> (string) $cell
															);
//						echo 'NEW EXPRESSION ',$ExprID,'<br />';
					} else {
						$expression = $this->_expressions[$ExprID];

						$cell = $this->_referenceHelper->updateFormulaReferences( $expression['formula'],
																				  'A1',
																				  $cellAttributes->Col - $expression['column'],
																				  $cellAttributes->Row - $expression['row'],
																				  $worksheetName
																				);
//						echo 'SHARED EXPRESSION ',$ExprID,'<br />';
//						echo 'New Value is ',$cell,'<br />';
					}
				}
				switch($ValueType) {
					case '20' :
						$type = PHPExcel_Cell_DataType::TYPE_BOOL;
						$cell = ($cell == 'TRUE') ? True : False;
						break;
					case '40' :
						$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
						break;
					case '60' :
						$type = PHPExcel_Cell_DataType::TYPE_STRING;
						break;
				}
				$objPHPExcel->getActiveSheet()->getCell($column.$row)->setValueExplicit($cell,$type);
			}

			$worksheetID++;
		}


		// Return
		return $objPHPExcel;
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Reader_Gnumeric
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}
}
