<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
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
}

/**
 * PHPExcel_Reader_OOCalc
 *
 * @category	PHPExcel
 * @package		PHPExcel_Reader
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_OOCalc implements PHPExcel_Reader_IReader
{
	/**
	 * Read data only?
	 * Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
	 *		or whether it should read both data and formatting
	 *
	 * @var	boolean
	 */
	private $_readDataOnly = false;

	/**
	 * Restrict which sheets should be loaded?
	 * This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
	 *
	 * @var	array of string
	 */
	private $_loadSheetsOnly = null;

	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_styles = array();

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;


	/**
	 * Create a new PHPExcel_Reader_OOCalc
	 */
	public function __construct() {
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
	}


	/**
	 * Read data only?
	 *		If this is true, then the Reader will only read data values for cells, it will not read any formatting information.
	 *		If false (the default) it will read data and formatting.
	 *
	 * @return	boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}


	/**
	 * Set read data only
	 *		Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting information.
	 *		Set to false (the default) to advise the Reader to read both data and formatting for cells.
	 *
	 * @param	boolean	$pValue
	 * @return	PHPExcel_Reader_OOCalc
	 */
	public function setReadDataOnly($pValue = false) {
		$this->_readDataOnly = $pValue;
		return $this;
	}


	/**
	 * Get which sheets to load
	 *		Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
	 *			indicating that all worksheets in the workbook should be loaded.
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
	 *		This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
	 *		If NULL, then it tells the Reader to read all worksheets in the workbook
	 *
	 * @return PHPExcel_Reader_OOCalc
	 */
	public function setLoadSheetsOnly($value = null)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
		return $this;
	}


	/**
	 * Set all sheets to load
	 *		Tells the Reader to load all worksheets from the workbook.
	 *
	 * @return PHPExcel_Reader_OOCalc
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
	 * @return PHPExcel_Reader_OOCalc
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
		return $this;
	}


	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFileName
	 * @return 	boolean
	 * @throws Exception
	 */
	public function canRead($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Check if zip class exists
		if (!class_exists('ZipArchive')) {
			throw new Exception("ZipArchive library is not enabled");
		}

		// Load file
		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {
			// check if it is an OOXML archive
			$mimeType = $zip->getFromName("mimetype");

			$zip->close();

			return ($mimeType === 'application/vnd.oasis.opendocument.spreadsheet');
		}

		return false;
	}


	/**
	 * Reads names of the worksheets from a file, without parsing the whole file to a PHPExcel object
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */
	public function listWorksheetNames($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$worksheetNames = array();

		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {

			$xml = simplexml_load_string($zip->getFromName("content.xml"));
			$namespacesContent = $xml->getNamespaces(true);

			$workbook = $xml->children($namespacesContent['office']);
			foreach($workbook->body->spreadsheet as $workbookData) {
				$workbookData = $workbookData->children($namespacesContent['table']);
				foreach($workbookData->table as $worksheetDataSet) {
					$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);

					$worksheetNames[] = $worksheetDataAttributes['name'];
				}
			}
		}

		return $worksheetNames;
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


	/**
	 * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
	 *
	 * @param   string     $pFilename
	 * @throws   Exception
	 */
	public function listWorksheetInfo($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$worksheetInfo = array();

		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {

			$xml = simplexml_load_string($zip->getFromName("content.xml"));
			$namespacesContent = $xml->getNamespaces(true);

			$workbook = $xml->children($namespacesContent['office']);
			foreach($workbook->body->spreadsheet as $workbookData) {
				$workbookData = $workbookData->children($namespacesContent['table']);
				foreach($workbookData->table as $worksheetDataSet) {
					$worksheetData = $worksheetDataSet->children($namespacesContent['table']);
					$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);

					$tmpInfo = array();
					$tmpInfo['worksheetName'] = (string) $worksheetDataAttributes['name'];
					$tmpInfo['lastColumnLetter'] = 'A';
					$tmpInfo['lastColumnIndex'] = 0;
					$tmpInfo['totalRows'] = 0;
					$tmpInfo['totalColumns'] = 0;

					$rowIndex = 0;
					foreach ($worksheetData as $key => $rowData) {
						switch ($key) {
							case 'table-row' :
								$columnIndex = 0;

								foreach ($rowData as $key => $cellData) {
									$tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
									++$columnIndex;
								}
								++$rowIndex;
								$tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
								break;
						}
					}

					$tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
					$tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

					$worksheetInfo[] = $tmpInfo;
				}
			}
		}

		return $worksheetInfo;
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

		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {
//			echo '<h1>Meta Information</h1>';
			$xml = simplexml_load_string($zip->getFromName("meta.xml"));
			$namespacesMeta = $xml->getNamespaces(true);
//			echo '<pre>';
//			print_r($namespacesMeta);
//			echo '</pre><hr />';

			$docProps = $objPHPExcel->getProperties();
			$officeProperty = $xml->children($namespacesMeta['office']);
			foreach($officeProperty as $officePropertyData) {
				$officePropertyDC = array();
				if (isset($namespacesMeta['dc'])) {
					$officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
				}
				foreach($officePropertyDC as $propertyName => $propertyValue) {
					switch ($propertyName) {
						case 'title' :
								$docProps->setTitle($propertyValue);
								break;
						case 'subject' :
								$docProps->setSubject($propertyValue);
								break;
						case 'creator' :
								$docProps->setCreator($propertyValue);
								$docProps->setLastModifiedBy($propertyValue);
								break;
						case 'date' :
								$creationDate = strtotime($propertyValue);
								$docProps->setCreated($creationDate);
								$docProps->setModified($creationDate);
								break;
						case 'description' :
								$docProps->setDescription($propertyValue);
								break;
					}
				}
				$officePropertyMeta = array();
				if (isset($namespacesMeta['dc'])) {
					$officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
				}
				foreach($officePropertyMeta as $propertyName => $propertyValue) {
					$propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);
					switch ($propertyName) {
						case 'initial-creator' :
								$docProps->setCreator($propertyValue);
								break;
						case 'keyword' :
								$docProps->setKeywords($propertyValue);
								break;
						case 'creation-date' :
								$creationDate = strtotime($propertyValue);
								$docProps->setCreated($creationDate);
								break;
						case 'user-defined' :
								$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
								foreach ($propertyValueAttributes as $key => $value) {
									if ($key == 'name') {
										$propertyValueName = (string) $value;
									} elseif($key == 'value-type') {
										switch ($value) {
											case 'date'	:
												$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'date');
												$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE;
												break;
											case 'boolean'	:
												$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'bool');
												$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN;
												break;
											case 'float'	:
												$propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue,'r4');
												$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_FLOAT;
												break;
											default :
												$propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
										}
									}
								}
								$docProps->setCustomProperty($propertyValueName,$propertyValue,$propertyValueType);
								break;
					}
				}
			}


//			echo '<h1>Workbook Content</h1>';
			$xml = simplexml_load_string($zip->getFromName("content.xml"));
			$namespacesContent = $xml->getNamespaces(true);
//			echo '<pre>';
//			print_r($namespacesContent);
//			echo '</pre><hr />';

			$workbook = $xml->children($namespacesContent['office']);
			foreach($workbook->body->spreadsheet as $workbookData) {
				$workbookData = $workbookData->children($namespacesContent['table']);
				$worksheetID = 0;
				foreach($workbookData->table as $worksheetDataSet) {
					$worksheetData = $worksheetDataSet->children($namespacesContent['table']);
//					print_r($worksheetData);
//					echo '<br />';
					$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
//					print_r($worksheetDataAttributes);
//					echo '<br />';
					if ((isset($this->_loadSheetsOnly)) && (isset($worksheetDataAttributes['name'])) &&
						(!in_array($worksheetDataAttributes['name'], $this->_loadSheetsOnly))) {
						continue;
					}

//					echo '<h2>Worksheet '.$worksheetDataAttributes['name'].'</h2>';
					// Create new Worksheet
					$objPHPExcel->createSheet();
					$objPHPExcel->setActiveSheetIndex($worksheetID);
					if (isset($worksheetDataAttributes['name'])) {
						$worksheetName = (string) $worksheetDataAttributes['name'];
						//	Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
						//		formula cells... during the load, all formulae should be correct, and we're simply
						//		bringing the worksheet name in line with the formula, not the reverse
						$objPHPExcel->getActiveSheet()->setTitle($worksheetName,false);
					}

					$rowID = 1;
					foreach($worksheetData as $key => $rowData) {
//						echo '<b>'.$key.'</b><br />';
						switch ($key) {
							case 'table-header-rows':
								foreach ($rowData as $key=>$cellData) {
									$rowData = $cellData;
									break;
								}
							case 'table-row' :
								$columnID = 'A';
								foreach($rowData as $key => $cellData) {
									if ($this->getReadFilter() !== NULL) {
										if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
											continue;
										}
									}

//									echo '<b>'.$columnID.$rowID.'</b><br />';
									$cellDataText = $cellData->children($namespacesContent['text']);
									$cellDataOffice = $cellData->children($namespacesContent['office']);
									$cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
									$cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);

//									echo 'Office Attributes: ';
//									print_r($cellDataOfficeAttributes);
//									echo '<br />Table Attributes: ';
//									print_r($cellDataTableAttributes);
//									echo '<br />Cell Data Text';
//									print_r($cellDataText);
//									echo '<br />';
//
									$type = $formatting = $hyperlink = null;
									$hasCalculatedValue = false;
									$cellDataFormula = '';
									if (isset($cellDataTableAttributes['formula'])) {
										$cellDataFormula = $cellDataTableAttributes['formula'];
										$hasCalculatedValue = true;
									}

									if (isset($cellDataOffice->annotation)) {
//										echo 'Cell has comment<br />';
										$annotationText = $cellDataOffice->annotation->children($namespacesContent['text']);
										$textArray = array();
										foreach($annotationText as $t) {
											foreach($t->span as $text) {
												$textArray[] = (string)$text;
											}
										}
										$text = implode("\n",$textArray);
//										echo $text,'<br />';
										$objPHPExcel->getActiveSheet()->getComment( $columnID.$rowID )
//																		->setAuthor( $author )
																		->setText($this->_parseRichText($text) );
									}

									if (isset($cellDataText->p)) {
										// Consolodate if there are multiple p records (maybe with spans as well)
										$dataArray = array();
										// Text can have multiple text:p and within those, multiple text:span.
										// text:p newlines, but text:span does not.
										// Also, here we assume there is no text data is span fields are specified, since
										// we have no way of knowing proper positioning anyway.
										foreach ($cellDataText->p as $pData) {
											if (isset($pData->span)) {
												// span sections do not newline, so we just create one large string here
												$spanSection = "";
												foreach ($pData->span as $spanData) {
													$spanSection .= $spanData;
												}
												array_push($dataArray, $spanSection);
											} else {
												array_push($dataArray, $pData);
											}
										}
										$allCellDataText = implode($dataArray, "\n");

//										echo 'Value Type is '.$cellDataOfficeAttributes['value-type'].'<br />';
										switch ($cellDataOfficeAttributes['value-type']) {
 											case 'string' :
													$type = PHPExcel_Cell_DataType::TYPE_STRING;
													$dataValue = $allCellDataText;
													if (isset($dataValue->a)) {
														$dataValue = $dataValue->a;
														$cellXLinkAttributes = $dataValue->attributes($namespacesContent['xlink']);
														$hyperlink = $cellXLinkAttributes['href'];
													}
													break;
											case 'boolean' :
													$type = PHPExcel_Cell_DataType::TYPE_BOOL;
													$dataValue = ($allCellDataText == 'TRUE') ? True : False;
													break;
											case 'float' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
													$dataValue = (float) $cellDataOfficeAttributes['value'];
													if (floor($dataValue) == $dataValue) {
														$dataValue = (integer) $dataValue;
													}
													break;
											case 'date' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
												    $dateObj = new DateTime($cellDataOfficeAttributes['date-value'], $GMT);
													$dateObj->setTimeZone($timezoneObj);
													list($year,$month,$day,$hour,$minute,$second) = explode(' ',$dateObj->format('Y m d H i s'));
													$dataValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year,$month,$day,$hour,$minute,$second);
													if ($dataValue != floor($dataValue)) {
														$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15.' '.PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
													} else {
														$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15;
													}
													break;
											case 'time' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
													$dataValue = PHPExcel_Shared_Date::PHPToExcel(strtotime('01-01-1970 '.implode(':',sscanf($cellDataOfficeAttributes['time-value'],'PT%dH%dM%dS'))));
													$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
													break;
										}
//										echo 'Data value is '.$dataValue.'<br />';
//										if ($hyperlink !== NULL) {
//											echo 'Hyperlink is '.$hyperlink.'<br />';
//										}
									}

									if ($hasCalculatedValue) {
										$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
//										echo 'Formula: '.$cellDataFormula.'<br />';
										$cellDataFormula = substr($cellDataFormula,strpos($cellDataFormula,':=')+1);
										$temp = explode('"',$cellDataFormula);
										$tKey = false;
										foreach($temp as &$value) {
											//	Only replace in alternate array entries (i.e. non-quoted blocks)
											if ($tKey = !$tKey) {
												$value = preg_replace('/\[\.(.*):\.(.*)\]/Ui','$1:$2',$value);
												$value = preg_replace('/\[\.(.*)\]/Ui','$1',$value);
												$value = PHPExcel_Calculation::_translateSeparator(';',',',$value,$inBraces);
											}
										}
										unset($value);
										//	Then rebuild the formula string
										$cellDataFormula = implode('"',$temp);
//										echo 'Adjusted Formula: '.$cellDataFormula.'<br />';
									}

									$repeats = (isset($cellDataTableAttributes['number-columns-repeated'])) ?
										$cellDataTableAttributes['number-columns-repeated'] : 1;
									if ($type !== NULL) {
										for ($i = 0; $i < $repeats; ++$i) {
											if ($i > 0) {
												++$columnID;
											}
											$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $dataValue),$type);
											if ($hasCalculatedValue) {
//												echo 'Forumla result is '.$dataValue.'<br />';
												$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setCalculatedValue($dataValue);
											}
											if (($cellDataOfficeAttributes['value-type'] == 'date') ||
												($cellDataOfficeAttributes['value-type'] == 'time')) {
												$objPHPExcel->getActiveSheet()->getStyle($columnID.$rowID)->getNumberFormat()->setFormatCode($formatting);
											}
											if ($hyperlink !== NULL) {
												$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->getHyperlink()->setUrl($hyperlink);
											}
										}
									}

									//	Merged cells
									if ((isset($cellDataTableAttributes['number-columns-spanned'])) || (isset($cellDataTableAttributes['number-rows-spanned']))) {
										$columnTo = $columnID;
										if (isset($cellDataTableAttributes['number-columns-spanned'])) {
											$columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-spanned'] -2);
										}
										$rowTo = $rowID;
										if (isset($cellDataTableAttributes['number-rows-spanned'])) {
											$rowTo = $rowTo + $cellDataTableAttributes['number-rows-spanned'] - 1;
										}
										$cellRange = $columnID.$rowID.':'.$columnTo.$rowTo;
										$objPHPExcel->getActiveSheet()->mergeCells($cellRange);
									}

									++$columnID;
								}
								++$rowID;
								break;
						}
					}
					++$worksheetID;
				}
			}

		}

		// Return
		return $objPHPExcel;
	}


	private function _parseRichText($is = '') {
		$value = new PHPExcel_RichText();

		$value->createText($is);

		return $value;
	}

}
