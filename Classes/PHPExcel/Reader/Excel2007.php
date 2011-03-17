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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
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
 *	PHPExcel_Reader_Excel2007
 *
 *	@category	PHPExcel
 *	@package	PHPExcel_Reader
 *	@copyright	Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2007 implements PHPExcel_Reader_IReader
{
	/**
	 *	Read data only?
	 *	Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
	 *		or whether it should read both data and formatting
	 *
	 *	@var	boolean
	 */
	private $_readDataOnly = false;

	/**
	 *	Restrict which sheets should be loaded?
	 *	This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
	 *
	 *	@var array of string
	 */
	private $_loadSheetsOnly = null;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;

	/**
	 * PHPExcel_ReferenceHelper instance
	 *
	 * @var PHPExcel_ReferenceHelper
	 */
	private $_referenceHelper = null;

	/**
	 * PHPExcel_Reader_Excel2007_Theme instance
	 *
	 * @var PHPExcel_Reader_Excel2007_Theme
	 */
	private static $_theme = null;


	/**
	 *	Read data only?
	 *		If this is true, then the Reader will only read data values for cells, it will not read any formatting information.
	 *		If false (the default) it will read data and formatting.
	 *
	 *	@return	boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}

	/**
	 *	Set read data only
	 *		Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting information.
	 *		Set to false (the default) to advise the Reader to read both data and formatting for cells.
	 *
	 *	@param	boolean	$pValue
	 *
	 *	@return	PHPExcel_Reader_Excel2007
	 */
	public function setReadDataOnly($pValue = false) {
		$this->_readDataOnly = $pValue;
		return $this;
	}

	/**
	 *	Get which sheets to load
	 *		Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
	 *			indicating that all worksheets in the workbook should be loaded.
	 *
	 *	@return mixed
	 */
	public function getLoadSheetsOnly()
	{
		return $this->_loadSheetsOnly;
	}

	/**
	 *	Set which sheets to load
	 *
	 *	@param mixed $value
	 *		This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
	 *		If NULL, then it tells the Reader to read all worksheets in the workbook
	 *
	 *	@return PHPExcel_Reader_Excel2007
	 */
	public function setLoadSheetsOnly($value = null)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
		return $this;
	}

	/**
	 *	Set all sheets to load
	 *		Tells the Reader to load all worksheets from the workbook.
	 *
	 *	@return PHPExcel_Reader_Excel2007
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
	 * @return PHPExcel_Reader_Excel2007
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
		return $this;
	}

	/**
	 * Create a new PHPExcel_Reader_Excel2007 instance
	 */
	public function __construct() {
		$this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
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
		if (!class_exists('ZipArchive')) {
			return false;
		}

		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$xl = false;
		// Load file
		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {
			// check if it is an OOXML archive
			$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels"));
			if ($rels) {
				foreach ($rels->Relationship as $rel) {
					switch ($rel["Type"]) {
						case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
							if (basename($rel["Target"]) == 'workbook.xml') {
								$xl = true;
							}
							break;

					}
				}
			}
			$zip->close();
		}

		return $xl;
	}

	private static function _castToBool($c) {
//		echo 'Initial Cast to Boolean<br />';
		$value = isset($c->v) ? (string) $c->v : null;
		if ($value == '0') {
			return false;
		} elseif ($value == '1') {
			return true;
		} else {
			return (bool)$c->v;
		}
		return $value;
	}	//	function _castToBool()

	private static function _castToError($c) {
//		echo 'Initial Cast to Error<br />';
		return isset($c->v) ? (string) $c->v : null;;
	}	//	function _castToError()

	private static function _castToString($c) {
//		echo 'Initial Cast to String<br />';
		return isset($c->v) ? (string) $c->v : null;;
	}	//	function _castToString()

	private function _castToFormula($c,$r,&$cellDataType,&$value,&$calculatedValue,&$sharedFormulas,$castBaseType) {
//		echo '<font color="darkgreen">Formula</font><br />';
//		echo '$c->f is '.$c->f.'<br />';
		$cellDataType 		= 'f';
		$value 				= "={$c->f}";
		$calculatedValue 	= self::$castBaseType($c);

		// Shared formula?
		if (isset($c->f['t']) && strtolower((string)$c->f['t']) == 'shared') {
//			echo '<font color="darkgreen">SHARED FORMULA</font><br />';
			$instance = (string)$c->f['si'];

//			echo 'Instance ID = '.$instance.'<br />';
//
//			echo 'Shared Formula Array:<pre>';
//			print_r($sharedFormulas);
//			echo '</pre>';
			if (!isset($sharedFormulas[(string)$c->f['si']])) {
//				echo '<font color="darkgreen">SETTING NEW SHARED FORMULA</font><br />';
//				echo 'Master is '.$r.'<br />';
//				echo 'Formula is '.$value.'<br />';
				$sharedFormulas[$instance] = array(	'master' => $r,
													'formula' => $value
												  );
//				echo 'New Shared Formula Array:<pre>';
//				print_r($sharedFormulas);
//				echo '</pre>';
			} else {
//				echo '<font color="darkgreen">GETTING SHARED FORMULA</font><br />';
//				echo 'Master is '.$sharedFormulas[$instance]['master'].'<br />';
//				echo 'Formula is '.$sharedFormulas[$instance]['formula'].'<br />';
				$master = PHPExcel_Cell::coordinateFromString($sharedFormulas[$instance]['master']);
				$current = PHPExcel_Cell::coordinateFromString($r);

				$difference = array(0, 0);
				$difference[0] = PHPExcel_Cell::columnIndexFromString($current[0]) - PHPExcel_Cell::columnIndexFromString($master[0]);
				$difference[1] = $current[1] - $master[1];

				$value = $this->_referenceHelper->updateFormulaReferences(	$sharedFormulas[$instance]['formula'],
																			'A1',
																			$difference[0],
																			$difference[1]
																		 );
//				echo 'Adjusted Formula is '.$value.'<br />';
			}
		}
	}

	public function _getFromZipArchive(ZipArchive $archive, $fileName = '')
	{
		// Root-relative paths
		if (strpos($fileName, '//') !== false)
		{
			$fileName = substr($fileName, strpos($fileName, '//') + 1);
		}
		$fileName = PHPExcel_Shared_File::realpath($fileName);

		// Apache POI fixes
		$contents = $archive->getFromName($fileName);
		if ($contents === false)
		{
			$contents = $archive->getFromName(substr($fileName, 1));
		}

		return $contents;
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
		$zip->open($pFilename);

		$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels")); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
					$xmlWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

					if ($xmlWorkbook->sheets) {
						foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
							// Check if sheet should be skipped
							$worksheetNames[] = (string) $eleSheet["name"];
						}
					}
			}
		}

		$zip->close();

		return $worksheetNames;
	}


	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Initialisations
		$excel = new PHPExcel;
		$excel->removeSheetByIndex(0);
		if (!$this->_readDataOnly) {
			$excel->removeCellStyleXfByIndex(0); // remove the default style
			$excel->removeCellXfByIndex(0); // remove the default style
		}
		$zip = new ZipArchive;
		$zip->open($pFilename);

		//	Read the theme first, because we need the colour scheme when reading the styles
		$wbRels = simplexml_load_string($this->_getFromZipArchive($zip, "xl/_rels/workbook.xml.rels")); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($wbRels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme":
					$themeOrderArray = array('lt1','dk1','lt2','dk2');
					$themeOrderAdditional = count($themeOrderArray);

					$xmlTheme = simplexml_load_string($this->_getFromZipArchive($zip, "xl/{$rel['Target']}"));
					if (is_object($xmlTheme)) {
						$xmlThemeName = $xmlTheme->attributes();
						$xmlTheme = $xmlTheme->children("http://schemas.openxmlformats.org/drawingml/2006/main");
						$themeName = (string)$xmlThemeName['name'];

						$colourScheme = $xmlTheme->themeElements->clrScheme->attributes();
						$colourSchemeName = (string)$colourScheme['name'];
						$colourScheme = $xmlTheme->themeElements->clrScheme->children("http://schemas.openxmlformats.org/drawingml/2006/main");

						$themeColours = array();
						foreach ($colourScheme as $k => $xmlColour) {
							$themePos = array_search($k,$themeOrderArray);
							if ($themePos === false) {
								$themePos = $themeOrderAdditional++;
							}
							if (isset($xmlColour->sysClr)) {
								$xmlColourData = $xmlColour->sysClr->attributes();
								$themeColours[$themePos] = $xmlColourData['lastClr'];
							} elseif (isset($xmlColour->srgbClr)) {
								$xmlColourData = $xmlColour->srgbClr->attributes();
								$themeColours[$themePos] = $xmlColourData['val'];
							}
						}
						self::$_theme = new PHPExcel_Reader_Excel2007_Theme($themeName,$colourSchemeName,$themeColours);
					}
					break;
			}
		}

		$rels = simplexml_load_string($this->_getFromZipArchive($zip, "_rels/.rels")); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"));
					if (is_object($xmlCore)) {
						$xmlCore->registerXPathNamespace("dc", "http://purl.org/dc/elements/1.1/");
						$xmlCore->registerXPathNamespace("dcterms", "http://purl.org/dc/terms/");
						$xmlCore->registerXPathNamespace("cp", "http://schemas.openxmlformats.org/package/2006/metadata/core-properties");
						$docProps = $excel->getProperties();
						$docProps->setCreator((string) self::array_item($xmlCore->xpath("dc:creator")));
						$docProps->setLastModifiedBy((string) self::array_item($xmlCore->xpath("cp:lastModifiedBy")));
						$docProps->setCreated(strtotime(self::array_item($xmlCore->xpath("dcterms:created")))); //! respect xsi:type
						$docProps->setModified(strtotime(self::array_item($xmlCore->xpath("dcterms:modified")))); //! respect xsi:type
						$docProps->setTitle((string) self::array_item($xmlCore->xpath("dc:title")));
						$docProps->setDescription((string) self::array_item($xmlCore->xpath("dc:description")));
						$docProps->setSubject((string) self::array_item($xmlCore->xpath("dc:subject")));
						$docProps->setKeywords((string) self::array_item($xmlCore->xpath("cp:keywords")));
						$docProps->setCategory((string) self::array_item($xmlCore->xpath("cp:category")));
					}
				break;

				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"));
					if (is_object($xmlCore)) {
						$docProps = $excel->getProperties();
						if (isset($xmlCore->Company))
							$docProps->setCompany((string) $xmlCore->Company);
						if (isset($xmlCore->Manager))
							$docProps->setManager((string) $xmlCore->Manager);
					}
				break;

				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties":
					$xmlCore = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"));
					if (is_object($xmlCore)) {
						$docProps = $excel->getProperties();
						foreach ($xmlCore as $xmlProperty) {
							$cellDataOfficeAttributes = $xmlProperty->attributes();
							if (isset($cellDataOfficeAttributes['name'])) {
								$propertyName = (string) $cellDataOfficeAttributes['name'];
								$cellDataOfficeChildren = $xmlProperty->children('http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');
								$attributeType = $cellDataOfficeChildren->getName();
								$attributeValue = (string) $cellDataOfficeChildren->{$attributeType};
								$attributeValue = PHPExcel_DocumentProperties::convertProperty($attributeValue,$attributeType);
								$attributeType = PHPExcel_DocumentProperties::convertPropertyType($attributeType);
								$docProps->setCustomProperty($propertyName,$attributeValue,$attributeType);
							}
						}
					}
				break;

				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
					$dir = dirname($rel["Target"]);
					$relsWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/_rels/" . basename($rel["Target"]) . ".rels"));  //~ http://schemas.openxmlformats.org/package/2006/relationships");
					$relsWorkbook->registerXPathNamespace("rel", "http://schemas.openxmlformats.org/package/2006/relationships");

					$sharedStrings = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings']"));
					$xmlStrings = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$xpath[Target]"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					if (isset($xmlStrings) && isset($xmlStrings->si)) {
						foreach ($xmlStrings->si as $val) {
							if (isset($val->t)) {
								$sharedStrings[] = PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $val->t );
							} elseif (isset($val->r)) {
								$sharedStrings[] = $this->_parseRichText($val);
							}
						}
					}

					$worksheets = array();
					foreach ($relsWorkbook->Relationship as $ele) {
						if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet") {
							$worksheets[(string) $ele["Id"]] = $ele["Target"];
						}
					}

					$styles 	= array();
					$cellStyles = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles']"));
					$xmlStyles = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$xpath[Target]")); //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					$numFmts = null;
					if ($xmlStyles && $xmlStyles->numFmts[0]) {
						$numFmts = $xmlStyles->numFmts[0];
					}
					if (isset($numFmts) && !is_null($numFmts)) {
						$numFmts->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					}
					if (!$this->_readDataOnly && $xmlStyles) {
						foreach ($xmlStyles->cellXfs->xf as $xf) {
							$numFmt = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;

							if ($xf["numFmtId"]) {
								if (isset($numFmts)) {
									$tmpNumFmt = self::array_item($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));

									if (isset($tmpNumFmt["formatCode"])) {
										$numFmt = (string) $tmpNumFmt["formatCode"];
									}
								}

								if ((int)$xf["numFmtId"] < 164) {
									$numFmt = PHPExcel_Style_NumberFormat::builtInFormatCode((int)$xf["numFmtId"]);
								}
							}
							//$numFmt = str_replace('mm', 'i', $numFmt);
							//$numFmt = str_replace('h', 'H', $numFmt);

							$style = (object) array(
								"numFmt" => $numFmt,
								"font" => $xmlStyles->fonts->font[intval($xf["fontId"])],
								"fill" => $xmlStyles->fills->fill[intval($xf["fillId"])],
								"border" => $xmlStyles->borders->border[intval($xf["borderId"])],
								"alignment" => $xf->alignment,
								"protection" => $xf->protection,
							);
							$styles[] = $style;

							// add style to cellXf collection
							$objStyle = new PHPExcel_Style;
							self::_readStyle($objStyle, $style);
							$excel->addCellXf($objStyle);
						}

						foreach ($xmlStyles->cellStyleXfs->xf as $xf) {
							$numFmt = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
							if ($numFmts && $xf["numFmtId"]) {
								$tmpNumFmt = self::array_item($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
								if (isset($tmpNumFmt["formatCode"])) {
									$numFmt = (string) $tmpNumFmt["formatCode"];
								} else if ((int)$xf["numFmtId"] < 165) {
									$numFmt = PHPExcel_Style_NumberFormat::builtInFormatCode((int)$xf["numFmtId"]);
								}
							}

							$cellStyle = (object) array(
								"numFmt" => $numFmt,
								"font" => $xmlStyles->fonts->font[intval($xf["fontId"])],
								"fill" => $xmlStyles->fills->fill[intval($xf["fillId"])],
								"border" => $xmlStyles->borders->border[intval($xf["borderId"])],
								"alignment" => $xf->alignment,
								"protection" => $xf->protection,
							);
							$cellStyles[] = $cellStyle;

							// add style to cellStyleXf collection
							$objStyle = new PHPExcel_Style;
							self::_readStyle($objStyle, $cellStyle);
							$excel->addCellStyleXf($objStyle);
						}
					}

					$dxfs = array();
					if (!$this->_readDataOnly && $xmlStyles) {
						if ($xmlStyles->dxfs) {
							foreach ($xmlStyles->dxfs->dxf as $dxf) {
								$style = new PHPExcel_Style;
								self::_readStyle($style, $dxf);
								$dxfs[] = $style;
							}
						}

						if ($xmlStyles->cellStyles)
						{
							foreach ($xmlStyles->cellStyles->cellStyle as $cellStyle) {
								if (intval($cellStyle['builtinId']) == 0) {
									if (isset($cellStyles[intval($cellStyle['xfId'])])) {
										// Set default style
										$style = new PHPExcel_Style;
										self::_readStyle($style, $cellStyles[intval($cellStyle['xfId'])]);

										// normal style, currently not using it for anything
									}
								}
							}
						}
					}

					$xmlWorkbook = simplexml_load_string($this->_getFromZipArchive($zip, "{$rel['Target']}"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

					// Set base date
					if ($xmlWorkbook->workbookPr) {
						PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
						if (isset($xmlWorkbook->workbookPr['date1904'])) {
							$date1904 = (string)$xmlWorkbook->workbookPr['date1904'];
							if ($date1904 == "true" || $date1904 == "1") {
								PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
							}
						}
					}

					$sheetId = 0; // keep track of new sheet id in final workbook
					$oldSheetId = -1; // keep track of old sheet id in final workbook
					$countSkippedSheets = 0; // keep track of number of skipped sheets
					$mapSheetId = array(); // mapping of sheet ids from old to new

					if ($xmlWorkbook->sheets)
					{
						foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
							++$oldSheetId;

							// Check if sheet should be skipped
							if (isset($this->_loadSheetsOnly) && !in_array((string) $eleSheet["name"], $this->_loadSheetsOnly)) {
								++$countSkippedSheets;
								$mapSheetId[$oldSheetId] = null;
								continue;
							}

							// Map old sheet id in original workbook to new sheet id.
							// They will differ if loadSheetsOnly() is being used
							$mapSheetId[$oldSheetId] = $oldSheetId - $countSkippedSheets;

							// Load sheet
							$docSheet = $excel->createSheet();
							$docSheet->setTitle((string) $eleSheet["name"]);
							$fileWorksheet = $worksheets[(string) self::array_item($eleSheet->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
							$xmlSheet = simplexml_load_string($this->_getFromZipArchive($zip, "$dir/$fileWorksheet"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

							$sharedFormulas = array();

							if (isset($eleSheet["state"]) && (string) $eleSheet["state"] != '') {
								$docSheet->setSheetState( (string) $eleSheet["state"] );
							}

							if (isset($xmlSheet->sheetViews) && isset($xmlSheet->sheetViews->sheetView)) {
							    if (isset($xmlSheet->sheetViews->sheetView['zoomScale'])) {
								    $docSheet->getSheetView()->setZoomScale( intval($xmlSheet->sheetViews->sheetView['zoomScale']) );
								}

							    if (isset($xmlSheet->sheetViews->sheetView['zoomScaleNormal'])) {
								    $docSheet->getSheetView()->setZoomScaleNormal( intval($xmlSheet->sheetViews->sheetView['zoomScaleNormal']) );
								}

								if (isset($xmlSheet->sheetViews->sheetView['showGridLines'])) {
									$docSheet->setShowGridLines((string)$xmlSheet->sheetViews->sheetView['showGridLines'] ? true : false);
								}

								if (isset($xmlSheet->sheetViews->sheetView['showRowColHeaders'])) {
									$docSheet->setShowRowColHeaders((string)$xmlSheet->sheetViews->sheetView['showRowColHeaders'] ? true : false);
								}

								if (isset($xmlSheet->sheetViews->sheetView['rightToLeft'])) {
									$docSheet->setRightToLeft((string)$xmlSheet->sheetViews->sheetView['rightToLeft'] ? true : false);
								}

								if (isset($xmlSheet->sheetViews->sheetView->pane)) {
								    if (isset($xmlSheet->sheetViews->sheetView->pane['topLeftCell'])) {
								        $docSheet->freezePane( (string)$xmlSheet->sheetViews->sheetView->pane['topLeftCell'] );
								    } else {
								        $xSplit = 0;
								        $ySplit = 0;

								        if (isset($xmlSheet->sheetViews->sheetView->pane['xSplit'])) {
								            $xSplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['xSplit']);
								        }

								    	if (isset($xmlSheet->sheetViews->sheetView->pane['ySplit'])) {
								            $ySplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['ySplit']);
								        }

								        $docSheet->freezePaneByColumnAndRow($xSplit, $ySplit);
								    }
								}

								if (isset($xmlSheet->sheetViews->sheetView->selection)) {
									if (isset($xmlSheet->sheetViews->sheetView->selection['sqref'])) {
										$sqref = (string)$xmlSheet->sheetViews->sheetView->selection['sqref'];
										$sqref = explode(' ', $sqref);
										$sqref = $sqref[0];
										$docSheet->setSelectedCells($sqref);
									}
								}

							}

							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->tabColor)) {
								if (isset($xmlSheet->sheetPr->tabColor['rgb'])) {
									$docSheet->getTabColor()->setARGB( (string)$xmlSheet->sheetPr->tabColor['rgb'] );
								}
							}

							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->outlinePr)) {
								if (isset($xmlSheet->sheetPr->outlinePr['summaryRight']) && $xmlSheet->sheetPr->outlinePr['summaryRight'] == false) {
									$docSheet->setShowSummaryRight(false);
								} else {
									$docSheet->setShowSummaryRight(true);
								}

								if (isset($xmlSheet->sheetPr->outlinePr['summaryBelow']) && $xmlSheet->sheetPr->outlinePr['summaryBelow'] == false) {
									$docSheet->setShowSummaryBelow(false);
								} else {
									$docSheet->setShowSummaryBelow(true);
								}
							}

							if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->pageSetUpPr)) {
								if (isset($xmlSheet->sheetPr->pageSetUpPr['fitToPage']) && $xmlSheet->sheetPr->pageSetUpPr['fitToPage'] == false) {
									$docSheet->getPageSetup()->setFitToPage(false);
								} else {
									$docSheet->getPageSetup()->setFitToPage(true);
								}
							}

							if (isset($xmlSheet->sheetFormatPr)) {
								if (isset($xmlSheet->sheetFormatPr['customHeight']) && ((string)$xmlSheet->sheetFormatPr['customHeight'] == '1' || strtolower((string)$xmlSheet->sheetFormatPr['customHeight']) == 'true') && isset($xmlSheet->sheetFormatPr['defaultRowHeight'])) {
									$docSheet->getDefaultRowDimension()->setRowHeight( (float)$xmlSheet->sheetFormatPr['defaultRowHeight'] );
								}
								if (isset($xmlSheet->sheetFormatPr['defaultColWidth'])) {
									$docSheet->getDefaultColumnDimension()->setWidth( (float)$xmlSheet->sheetFormatPr['defaultColWidth'] );
								}
							}

							if (isset($xmlSheet->cols) && !$this->_readDataOnly) {
								foreach ($xmlSheet->cols->col as $col) {
									for ($i = intval($col["min"]) - 1; $i < intval($col["max"]); ++$i) {
										if ($col["style"] && !$this->_readDataOnly) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setXfIndex(intval($col["style"]));
										}
										if ($col["bestFit"]) {
											//$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
										}
										if ($col["hidden"]) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setVisible(false);
										}
										if ($col["collapsed"]) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setCollapsed(true);
										}
										if ($col["outlineLevel"] > 0) {
											$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setOutlineLevel(intval($col["outlineLevel"]));
										}
										$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(floatval($col["width"]));

										if (intval($col["max"]) == 16384) {
											break;
										}
									}
								}
							}

							if (isset($xmlSheet->printOptions) && !$this->_readDataOnly) {
								if ($xmlSheet->printOptions['gridLinesSet'] == 'true' && $xmlSheet->printOptions['gridLinesSet'] == '1') {
									$docSheet->setShowGridlines(true);
								}

								if ($xmlSheet->printOptions['gridLines'] == 'true' || $xmlSheet->printOptions['gridLines'] == '1') {
									$docSheet->setPrintGridlines(true);
								}

								if ($xmlSheet->printOptions['horizontalCentered']) {
									$docSheet->getPageSetup()->setHorizontalCentered(true);
								}
								if ($xmlSheet->printOptions['verticalCentered']) {
									$docSheet->getPageSetup()->setVerticalCentered(true);
								}
							}

							if ($xmlSheet && $xmlSheet->sheetData && $xmlSheet->sheetData->row) {
								foreach ($xmlSheet->sheetData->row as $row) {
									if ($row["ht"] && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setRowHeight(floatval($row["ht"]));
									}
									if ($row["hidden"] && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setVisible(false);
									}
									if ($row["collapsed"]) {
										$docSheet->getRowDimension(intval($row["r"]))->setCollapsed(true);
									}
									if ($row["outlineLevel"] > 0) {
										$docSheet->getRowDimension(intval($row["r"]))->setOutlineLevel(intval($row["outlineLevel"]));
									}
									if ($row["s"] && !$this->_readDataOnly) {
										$docSheet->getRowDimension(intval($row["r"]))->setXfIndex(intval($row["s"]));
									}

									foreach ($row->c as $c) {
										$r 					= (string) $c["r"];
										$cellDataType 		= (string) $c["t"];
										$value				= null;
										$calculatedValue 	= null;

										// Read cell?
										if (!is_null($this->getReadFilter())) {
											$coordinates = PHPExcel_Cell::coordinateFromString($r);

											if (!$this->getReadFilter()->readCell($coordinates[0], $coordinates[1], $docSheet->getTitle())) {
												continue;
											}
										}

	//									echo '<b>Reading cell '.$coordinates[0].$coordinates[1].'</b><br />';
	//									print_r($c);
	//									echo '<br />';
	//									echo 'Cell Data Type is '.$cellDataType.': ';
	//
										// Read cell!
										switch ($cellDataType) {
											case "s":
	//											echo 'String<br />';
												if ((string)$c->v != '') {
													$value = $sharedStrings[intval($c->v)];

													if ($value instanceof PHPExcel_RichText) {
														$value = clone $value;
													}
												} else {
													$value = '';
												}

												break;
											case "b":
	//											echo 'Boolean<br />';
												if (!isset($c->f)) {
													$value = self::_castToBool($c);
												} else {
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToBool');
													if (isset($c->f['t'])) {
														$att = array();
														$att = $c->f;
														$docSheet->getCell($r)->setFormulaAttributes($att);
													}
	//												echo '$calculatedValue = '.$calculatedValue.'<br />';
												}
												break;
											case "inlineStr":
	//											echo 'Inline String<br />';
												$value = $this->_parseRichText($c->is);

												break;
											case "e":
	//											echo 'Error<br />';
												if (!isset($c->f)) {
													$value = self::_castToError($c);
												} else {
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToError');
	//												echo '$calculatedValue = '.$calculatedValue.'<br />';
												}

												break;

											default:
	//											echo 'Default<br />';
												if (!isset($c->f)) {
	//												echo 'Not a Formula<br />';
													$value = self::_castToString($c);
												} else {
	//												echo 'Treat as Formula<br />';
													// Formula
													$this->_castToFormula($c,$r,$cellDataType,$value,$calculatedValue,$sharedFormulas,'_castToString');
	//												echo '$calculatedValue = '.$calculatedValue.'<br />';
												}

												break;
										}
	//									echo 'Value is '.$value.'<br />';

										// Check for numeric values
										if (is_numeric($value) && $cellDataType != 's') {
											if ($value == (int)$value) $value = (int)$value;
											elseif ($value == (float)$value) $value = (float)$value;
											elseif ($value == (double)$value) $value = (double)$value;
										}

										// Rich text?
										if ($value instanceof PHPExcel_RichText && $this->_readDataOnly) {
											$value = $value->getPlainText();
										}

										$cell = $docSheet->getCell($r);
										// Assign value
										if ($cellDataType != '') {
											$cell->setValueExplicit($value, $cellDataType);
										} else {
											$cell->setValue($value);
										}
										if (!is_null($calculatedValue)) {
											$cell->setCalculatedValue($calculatedValue);
										}

										// Style information?
										if ($c["s"] && !$this->_readDataOnly) {
											// no style index means 0, it seems
											$cell->setXfIndex(isset($styles[intval($c["s"])]) ?
												intval($c["s"]) : 0);
										}
									}
								}
							}

							$conditionals = array();
							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->conditionalFormatting) {
								foreach ($xmlSheet->conditionalFormatting as $conditional) {
									foreach ($conditional->cfRule as $cfRule) {
										if (
											(
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_NONE ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_CELLIS ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT ||
												(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_EXPRESSION
											) && isset($dxfs[intval($cfRule["dxfId"])])
										) {
											$conditionals[(string) $conditional["sqref"]][intval($cfRule["priority"])] = $cfRule;
										}
									}
								}

								foreach ($conditionals as $ref => $cfRules) {
									ksort($cfRules);
									$conditionalStyles = array();
									foreach ($cfRules as $cfRule) {
										$objConditional = new PHPExcel_Style_Conditional();
										$objConditional->setConditionType((string)$cfRule["type"]);
										$objConditional->setOperatorType((string)$cfRule["operator"]);

										if ((string)$cfRule["text"] != '') {
											$objConditional->setText((string)$cfRule["text"]);
										}

										if (count($cfRule->formula) > 1) {
											foreach ($cfRule->formula as $formula) {
												$objConditional->addCondition((string)$formula);
											}
										} else {
											$objConditional->addCondition((string)$cfRule->formula);
										}
										$objConditional->setStyle(clone $dxfs[intval($cfRule["dxfId"])]);
										$conditionalStyles[] = $objConditional;
									}

									// Extract all cell references in $ref
									$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($ref);
									foreach ($aReferences as $reference) {
										$docSheet->getStyle($reference)->setConditionalStyles($conditionalStyles);
									}
								}
							}

							$aKeys = array("sheet", "objects", "scenarios", "formatCells", "formatColumns", "formatRows", "insertColumns", "insertRows", "insertHyperlinks", "deleteColumns", "deleteRows", "selectLockedCells", "sort", "autoFilter", "pivotTables", "selectUnlockedCells");
							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
								foreach ($aKeys as $key) {
									$method = "set" . ucfirst($key);
									$docSheet->getProtection()->$method($xmlSheet->sheetProtection[$key] == "true");
								}
							}

							if (!$this->_readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
								$docSheet->getProtection()->setPassword((string) $xmlSheet->sheetProtection["password"], true);
								if ($xmlSheet->protectedRanges->protectedRange) {
									foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
										$docSheet->protectCells((string) $protectedRange["sqref"], (string) $protectedRange["password"], true);
									}
								}
							}

							if ($xmlSheet && $xmlSheet->autoFilter && !$this->_readDataOnly) {
								$docSheet->setAutoFilter((string) $xmlSheet->autoFilter["ref"]);
							}

							if ($xmlSheet && $xmlSheet->mergeCells && $xmlSheet->mergeCells->mergeCell && !$this->_readDataOnly) {
								foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
									$docSheet->mergeCells((string) $mergeCell["ref"]);
								}
							}

							if ($xmlSheet && $xmlSheet->pageMargins && !$this->_readDataOnly) {
								$docPageMargins = $docSheet->getPageMargins();
								$docPageMargins->setLeft(floatval($xmlSheet->pageMargins["left"]));
								$docPageMargins->setRight(floatval($xmlSheet->pageMargins["right"]));
								$docPageMargins->setTop(floatval($xmlSheet->pageMargins["top"]));
								$docPageMargins->setBottom(floatval($xmlSheet->pageMargins["bottom"]));
								$docPageMargins->setHeader(floatval($xmlSheet->pageMargins["header"]));
								$docPageMargins->setFooter(floatval($xmlSheet->pageMargins["footer"]));
							}

							if ($xmlSheet && $xmlSheet->pageSetup && !$this->_readDataOnly) {
								$docPageSetup = $docSheet->getPageSetup();

								if (isset($xmlSheet->pageSetup["orientation"])) {
									$docPageSetup->setOrientation((string) $xmlSheet->pageSetup["orientation"]);
								}
								if (isset($xmlSheet->pageSetup["paperSize"])) {
									$docPageSetup->setPaperSize(intval($xmlSheet->pageSetup["paperSize"]));
								}
								if (isset($xmlSheet->pageSetup["scale"])) {
									$docPageSetup->setScale(intval($xmlSheet->pageSetup["scale"]), false);
								}
								if (isset($xmlSheet->pageSetup["fitToHeight"]) && intval($xmlSheet->pageSetup["fitToHeight"]) >= 0) {
									$docPageSetup->setFitToHeight(intval($xmlSheet->pageSetup["fitToHeight"]), false);
								}
								if (isset($xmlSheet->pageSetup["fitToWidth"]) && intval($xmlSheet->pageSetup["fitToWidth"]) >= 0) {
									$docPageSetup->setFitToWidth(intval($xmlSheet->pageSetup["fitToWidth"]), false);
								}
								if (isset($xmlSheet->pageSetup["firstPageNumber"]) && isset($xmlSheet->pageSetup["useFirstPageNumber"]) &&
									((string)$xmlSheet->pageSetup["useFirstPageNumber"] == 'true' || (string)$xmlSheet->pageSetup["useFirstPageNumber"] == '1')) {
									$docPageSetup->setFirstPageNumber(intval($xmlSheet->pageSetup["firstPageNumber"]));
								}
							}

							if ($xmlSheet && $xmlSheet->headerFooter && !$this->_readDataOnly) {
								$docHeaderFooter = $docSheet->getHeaderFooter();

								if (isset($xmlSheet->headerFooter["differentOddEven"]) &&
									((string)$xmlSheet->headerFooter["differentOddEven"] == 'true' || (string)$xmlSheet->headerFooter["differentOddEven"] == '1')) {
									$docHeaderFooter->setDifferentOddEven(true);
								} else {
									$docHeaderFooter->setDifferentOddEven(false);
								}
								if (isset($xmlSheet->headerFooter["differentFirst"]) &&
									((string)$xmlSheet->headerFooter["differentFirst"] == 'true' || (string)$xmlSheet->headerFooter["differentFirst"] == '1')) {
									$docHeaderFooter->setDifferentFirst(true);
								} else {
									$docHeaderFooter->setDifferentFirst(false);
								}
								if (isset($xmlSheet->headerFooter["scaleWithDoc"]) &&
									((string)$xmlSheet->headerFooter["scaleWithDoc"] == 'false' || (string)$xmlSheet->headerFooter["scaleWithDoc"] == '0')) {
									$docHeaderFooter->setScaleWithDocument(false);
								} else {
									$docHeaderFooter->setScaleWithDocument(true);
								}
								if (isset($xmlSheet->headerFooter["alignWithMargins"]) &&
									((string)$xmlSheet->headerFooter["alignWithMargins"] == 'false' || (string)$xmlSheet->headerFooter["alignWithMargins"] == '0')) {
									$docHeaderFooter->setAlignWithMargins(false);
								} else {
									$docHeaderFooter->setAlignWithMargins(true);
								}

								$docHeaderFooter->setOddHeader((string) $xmlSheet->headerFooter->oddHeader);
								$docHeaderFooter->setOddFooter((string) $xmlSheet->headerFooter->oddFooter);
								$docHeaderFooter->setEvenHeader((string) $xmlSheet->headerFooter->evenHeader);
								$docHeaderFooter->setEvenFooter((string) $xmlSheet->headerFooter->evenFooter);
								$docHeaderFooter->setFirstHeader((string) $xmlSheet->headerFooter->firstHeader);
								$docHeaderFooter->setFirstFooter((string) $xmlSheet->headerFooter->firstFooter);
							}

							if ($xmlSheet && $xmlSheet->rowBreaks && $xmlSheet->rowBreaks->brk && !$this->_readDataOnly) {
								foreach ($xmlSheet->rowBreaks->brk as $brk) {
									if ($brk["man"]) {
										$docSheet->setBreak("A$brk[id]", PHPExcel_Worksheet::BREAK_ROW);
									}
								}
							}
							if ($xmlSheet && $xmlSheet->colBreaks && $xmlSheet->colBreaks->brk && !$this->_readDataOnly) {
								foreach ($xmlSheet->colBreaks->brk as $brk) {
									if ($brk["man"]) {
										$docSheet->setBreak(PHPExcel_Cell::stringFromColumnIndex($brk["id"]) . "1", PHPExcel_Worksheet::BREAK_COLUMN);
									}
								}
							}

							if ($xmlSheet && $xmlSheet->dataValidations && !$this->_readDataOnly) {
								foreach ($xmlSheet->dataValidations->dataValidation as $dataValidation) {
								    // Uppercase coordinate
							    	$range = strtoupper($dataValidation["sqref"]);
									$rangeSet = explode(' ',$range);
									foreach($rangeSet as $range) {
										$stRange = $docSheet->shrinkRangeToFit($range);

										// Extract all cell references in $range
										$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($stRange);
										foreach ($aReferences as $reference) {
											// Create validation
											$docValidation = $docSheet->getCell($reference)->getDataValidation();
											$docValidation->setType((string) $dataValidation["type"]);
											$docValidation->setErrorStyle((string) $dataValidation["errorStyle"]);
											$docValidation->setOperator((string) $dataValidation["operator"]);
											$docValidation->setAllowBlank($dataValidation["allowBlank"] != 0);
											$docValidation->setShowDropDown($dataValidation["showDropDown"] == 0);
											$docValidation->setShowInputMessage($dataValidation["showInputMessage"] != 0);
											$docValidation->setShowErrorMessage($dataValidation["showErrorMessage"] != 0);
											$docValidation->setErrorTitle((string) $dataValidation["errorTitle"]);
											$docValidation->setError((string) $dataValidation["error"]);
											$docValidation->setPromptTitle((string) $dataValidation["promptTitle"]);
											$docValidation->setPrompt((string) $dataValidation["prompt"]);
											$docValidation->setFormula1((string) $dataValidation->formula1);
											$docValidation->setFormula2((string) $dataValidation->formula2);
										}
									}
								}
							}

							// Add hyperlinks
							$hyperlinks = array();
							if (!$this->_readDataOnly) {
								// Locate hyperlink relations
								if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
									$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
									foreach ($relsWorksheet->Relationship as $ele) {
										if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink") {
											$hyperlinks[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									}
								}

								// Loop through hyperlinks
								if ($xmlSheet && $xmlSheet->hyperlinks) {
									foreach ($xmlSheet->hyperlinks->hyperlink as $hyperlink) {
										// Link url
										$linkRel = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');

										foreach (PHPExcel_Cell::extractAllCellReferencesInRange($hyperlink['ref']) as $cellReference) {
											$cell = $docSheet->getCell( $cellReference );
											if (isset($linkRel['id'])) {
												$cell->getHyperlink()->setUrl( $hyperlinks[ (string)$linkRel['id'] ] );
											}
											if (isset($hyperlink['location'])) {
												$cell->getHyperlink()->setUrl( 'sheet://' . (string)$hyperlink['location'] );
											}

											// Tooltip
											if (isset($hyperlink['tooltip'])) {
												$cell->getHyperlink()->setTooltip( (string)$hyperlink['tooltip'] );
											}
										}
									}
								}
							}

							// Add comments
							$comments = array();
							$vmlComments = array();
							if (!$this->_readDataOnly) {
								// Locate comment relations
								if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
									$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
									foreach ($relsWorksheet->Relationship as $ele) {
									    if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments") {
											$comments[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									    if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing") {
											$vmlComments[(string)$ele["Id"]] = (string)$ele["Target"];
										}
									}
								}

								// Loop through comments
								foreach ($comments as $relName => $relPath) {
									// Load comments file
									$relPath = PHPExcel_Shared_File::realpath(dirname("$dir/$fileWorksheet") . "/" . $relPath);
									$commentsFile = simplexml_load_string($this->_getFromZipArchive($zip, $relPath) );

									// Utility variables
									$authors = array();

									// Loop through authors
									foreach ($commentsFile->authors->author as $author) {
										$authors[] = (string)$author;
									}

									// Loop through contents
									foreach ($commentsFile->commentList->comment as $comment) {
										$docSheet->getComment( (string)$comment['ref'] )->setAuthor( $authors[(string)$comment['authorId']] );
										$docSheet->getComment( (string)$comment['ref'] )->setText( $this->_parseRichText($comment->text) );
									}
								}

								// Loop through VML comments
							    foreach ($vmlComments as $relName => $relPath) {
									// Load VML comments file
									$relPath = PHPExcel_Shared_File::realpath(dirname("$dir/$fileWorksheet") . "/" . $relPath);
									$vmlCommentsFile = simplexml_load_string( $this->_getFromZipArchive($zip, $relPath) );
									$vmlCommentsFile->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

									$shapes = $vmlCommentsFile->xpath('//v:shape');
									foreach ($shapes as $shape) {
										$shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

										if (isset($shape['style'])) {
	    									$style        = (string)$shape['style'];
	    									$fillColor    = strtoupper( substr( (string)$shape['fillcolor'], 1 ) );
	    									$column       = null;
	    									$row          = null;

	    									$clientData   = $shape->xpath('.//x:ClientData');
	    									if (is_array($clientData) && count($clientData) > 0) {
	        									$clientData   = $clientData[0];

	        									if ( isset($clientData['ObjectType']) && (string)$clientData['ObjectType'] == 'Note' ) {
	        									    $temp = $clientData->xpath('.//x:Row');
	        									    if (is_array($temp)) $row = $temp[0];

	        									    $temp = $clientData->xpath('.//x:Column');
	        									    if (is_array($temp)) $column = $temp[0];
	        									}
	    									}

	    									if (!is_null($column) && !is_null($row)) {
	    									    // Set comment properties
	    									    $comment = $docSheet->getCommentByColumnAndRow($column, $row + 1);
	    									    $comment->getFillColor()->setRGB( $fillColor );

	    									    // Parse style
	    									    $styleArray = explode(';', str_replace(' ', '', $style));
	    									    foreach ($styleArray as $stylePair) {
	    									        $stylePair = explode(':', $stylePair);

	    									        if ($stylePair[0] == 'margin-left')     $comment->setMarginLeft($stylePair[1]);
	    									        if ($stylePair[0] == 'margin-top')      $comment->setMarginTop($stylePair[1]);
	    									        if ($stylePair[0] == 'width')           $comment->setWidth($stylePair[1]);
	    									        if ($stylePair[0] == 'height')          $comment->setHeight($stylePair[1]);
	    									        if ($stylePair[0] == 'visibility')      $comment->setVisible( $stylePair[1] == 'visible' );

	    									    }
	    									}
										}
									}
								}

								// Header/footer images
								if ($xmlSheet && $xmlSheet->legacyDrawingHF && !$this->_readDataOnly) {
									if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
										$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
										$vmlRelationship = '';

										foreach ($relsWorksheet->Relationship as $ele) {
											if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing") {
												$vmlRelationship = self::dir_add("$dir/$fileWorksheet", $ele["Target"]);
											}
										}

										if ($vmlRelationship != '') {
											// Fetch linked images
											$relsVML = simplexml_load_string($this->_getFromZipArchive($zip,  dirname($vmlRelationship) . '/_rels/' . basename($vmlRelationship) . '.rels' )); //~ http://schemas.openxmlformats.org/package/2006/relationships");
											$drawings = array();
											foreach ($relsVML->Relationship as $ele) {
												if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {
													$drawings[(string) $ele["Id"]] = self::dir_add($vmlRelationship, $ele["Target"]);
												}
											}

											// Fetch VML document
											$vmlDrawing = simplexml_load_string($this->_getFromZipArchive($zip, $vmlRelationship));
											$vmlDrawing->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

											$hfImages = array();

											$shapes = $vmlDrawing->xpath('//v:shape');
											foreach ($shapes as $shape) {
												$shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
												$imageData = $shape->xpath('//v:imagedata');
												$imageData = $imageData[0];

												$imageData = $imageData->attributes('urn:schemas-microsoft-com:office:office');
												$style = self::toCSSArray( (string)$shape['style'] );

												$hfImages[ (string)$shape['id'] ] = new PHPExcel_Worksheet_HeaderFooterDrawing();
												if (isset($imageData['title'])) {
													$hfImages[ (string)$shape['id'] ]->setName( (string)$imageData['title'] );
												}

												$hfImages[ (string)$shape['id'] ]->setPath("zip://$pFilename#" . $drawings[(string)$imageData['relid']], false);
												$hfImages[ (string)$shape['id'] ]->setResizeProportional(false);
												$hfImages[ (string)$shape['id'] ]->setWidth($style['width']);
												$hfImages[ (string)$shape['id'] ]->setHeight($style['height']);
												$hfImages[ (string)$shape['id'] ]->setOffsetX($style['margin-left']);
												$hfImages[ (string)$shape['id'] ]->setOffsetY($style['margin-top']);
												$hfImages[ (string)$shape['id'] ]->setResizeProportional(true);
											}

											$docSheet->getHeaderFooter()->setImages($hfImages);
										}
									}
								}

							}

	// TODO: Make sure drawings and graph are loaded differently!
							if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
								$relsWorksheet = simplexml_load_string($this->_getFromZipArchive($zip,  dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
								$drawings = array();
								foreach ($relsWorksheet->Relationship as $ele) {
									if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing") {
										$drawings[(string) $ele["Id"]] = self::dir_add("$dir/$fileWorksheet", $ele["Target"]);
									}
								}
								if ($xmlSheet->drawing && !$this->_readDataOnly) {
									foreach ($xmlSheet->drawing as $drawing) {
										$fileDrawing = $drawings[(string) self::array_item($drawing->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
										$relsDrawing = simplexml_load_string($this->_getFromZipArchive($zip,  dirname($fileDrawing) . "/_rels/" . basename($fileDrawing) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
										$images = array();

										if ($relsDrawing && $relsDrawing->Relationship) {
											foreach ($relsDrawing->Relationship as $ele) {
												if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {
													$images[(string) $ele["Id"]] = self::dir_add($fileDrawing, $ele["Target"]);
												}
											}
										}
										$xmlDrawing = simplexml_load_string($this->_getFromZipArchive($zip, $fileDrawing))->children("http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing");

										if ($xmlDrawing->oneCellAnchor) {
											foreach ($xmlDrawing->oneCellAnchor as $oneCellAnchor) {
												if ($oneCellAnchor->pic->blipFill) {
													$blip = $oneCellAnchor->pic->blipFill->children("http://schemas.openxmlformats.org/drawingml/2006/main")->blip;
													$xfrm = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->xfrm;
													$outerShdw = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->effectLst->outerShdw;
													$objDrawing = new PHPExcel_Worksheet_Drawing;
													$objDrawing->setName((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "name"));
													$objDrawing->setDescription((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "descr"));
													$objDrawing->setPath("zip://$pFilename#" . $images[(string) self::array_item($blip->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "embed")], false);
													$objDrawing->setCoordinates(PHPExcel_Cell::stringFromColumnIndex($oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1));
													$objDrawing->setOffsetX(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->colOff));
													$objDrawing->setOffsetY(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
													$objDrawing->setResizeProportional(false);
													$objDrawing->setWidth(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cx")));
													$objDrawing->setHeight(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cy")));
													if ($xfrm) {
														$objDrawing->setRotation(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($xfrm->attributes(), "rot")));
													}
													if ($outerShdw) {
														$shadow = $objDrawing->getShadow();
														$shadow->setVisible(true);
														$shadow->setBlurRadius(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "blurRad")));
														$shadow->setDistance(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "dist")));
														$shadow->setDirection(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($outerShdw->attributes(), "dir")));
														$shadow->setAlignment((string) self::array_item($outerShdw->attributes(), "algn"));
														$shadow->getColor()->setRGB(self::array_item($outerShdw->srgbClr->attributes(), "val"));
														$shadow->setAlpha(self::array_item($outerShdw->srgbClr->alpha->attributes(), "val") / 1000);
													}
													$objDrawing->setWorksheet($docSheet);
												}
											}
										}
										if ($xmlDrawing->twoCellAnchor) {
											foreach ($xmlDrawing->twoCellAnchor as $twoCellAnchor) {
												if ($twoCellAnchor->pic->blipFill) {
													$blip = $twoCellAnchor->pic->blipFill->children("http://schemas.openxmlformats.org/drawingml/2006/main")->blip;
													$xfrm = $twoCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->xfrm;
													$outerShdw = $twoCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->effectLst->outerShdw;
													$objDrawing = new PHPExcel_Worksheet_Drawing;
													$objDrawing->setName((string) self::array_item($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), "name"));
													$objDrawing->setDescription((string) self::array_item($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), "descr"));
													$objDrawing->setPath("zip://$pFilename#" . $images[(string) self::array_item($blip->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "embed")], false);
													$objDrawing->setCoordinates(PHPExcel_Cell::stringFromColumnIndex($twoCellAnchor->from->col) . ($twoCellAnchor->from->row + 1));
													$objDrawing->setOffsetX(PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->colOff));
													$objDrawing->setOffsetY(PHPExcel_Shared_Drawing::EMUToPixels($twoCellAnchor->from->rowOff));
													$objDrawing->setResizeProportional(false);

													$objDrawing->setWidth(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($xfrm->ext->attributes(), "cx")));
													$objDrawing->setHeight(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($xfrm->ext->attributes(), "cy")));

													if ($xfrm) {
														$objDrawing->setRotation(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($xfrm->attributes(), "rot")));
													}
													if ($outerShdw) {
														$shadow = $objDrawing->getShadow();
														$shadow->setVisible(true);
														$shadow->setBlurRadius(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "blurRad")));
														$shadow->setDistance(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "dist")));
														$shadow->setDirection(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($outerShdw->attributes(), "dir")));
														$shadow->setAlignment((string) self::array_item($outerShdw->attributes(), "algn"));
														$shadow->getColor()->setRGB(self::array_item($outerShdw->srgbClr->attributes(), "val"));
														$shadow->setAlpha(self::array_item($outerShdw->srgbClr->alpha->attributes(), "val") / 1000);
													}
													$objDrawing->setWorksheet($docSheet);
												}
											}
										}

									}
								}
							}

							// Loop through definedNames
							if ($xmlWorkbook->definedNames) {
								foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
									// Extract range
									$extractedRange = (string)$definedName;
									$extractedRange = preg_replace('/\'(\w+)\'\!/', '', $extractedRange);
									if (($spos = strpos($extractedRange,'!')) !== false) {
										$extractedRange = substr($extractedRange,0,$spos).str_replace('$', '', substr($extractedRange,$spos));
									} else {
										$extractedRange = str_replace('$', '', $extractedRange);
									}

									// Valid range?
									if (stripos((string)$definedName, '#REF!') !== false || $extractedRange == '') {
										continue;
									}

									// Some definedNames are only applicable if we are on the same sheet...
									if ((string)$definedName['localSheetId'] != '' && (string)$definedName['localSheetId'] == $sheetId) {
										// Switch on type
										switch ((string)$definedName['name']) {

											case '_xlnm._FilterDatabase':
												$docSheet->setAutoFilter($extractedRange);
												break;

											case '_xlnm.Print_Titles':
												// Split $extractedRange
												$extractedRange = explode(',', $extractedRange);

												// Set print titles
												foreach ($extractedRange as $range) {
													$matches = array();

													// check for repeating columns, e g. 'A:A' or 'A:D'
													if (preg_match('/^([A-Z]+)\:([A-Z]+)$/', $range, $matches)) {
														$docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($matches[1], $matches[2]));
													}
													// check for repeating rows, e.g. '1:1' or '1:5'
													elseif (preg_match('/^(\d+)\:(\d+)$/', $range, $matches)) {
														$docSheet->getPageSetup()->setRowsToRepeatAtTop(array($matches[1], $matches[2]));
													}
												}
												break;

											case '_xlnm.Print_Area':
												$rangeSets = explode(',', $extractedRange);		// FIXME: what if sheetname contains comma?
												$newRangeSets = array();
												foreach($rangeSets as $rangeSet) {
													$range = explode('!', $rangeSet);	// FIXME: what if sheetname contains exclamation mark?
													$rangeSet = isset($range[1]) ? $range[1] : $range[0];
													$newRangeSets[] = str_replace('$', '', $rangeSet);
												}
												$docSheet->getPageSetup()->setPrintArea(implode(',',$newRangeSets));
												break;

											default:
												break;
										}
									}
								}
							}

							// Next sheet id
							++$sheetId;
						}

						// Loop through definedNames
						if ($xmlWorkbook->definedNames) {
							foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
								// Extract range
								$extractedRange = (string)$definedName;
								$extractedRange = preg_replace('/\'(\w+)\'\!/', '', $extractedRange);
								if (($spos = strpos($extractedRange,'!')) !== false) {
									$extractedRange = substr($extractedRange,0,$spos).str_replace('$', '', substr($extractedRange,$spos));
								} else {
									$extractedRange = str_replace('$', '', $extractedRange);
								}

								// Valid range?
								if (stripos((string)$definedName, '#REF!') !== false || $extractedRange == '') {
									continue;
								}

								// Some definedNames are only applicable if we are on the same sheet...
								if ((string)$definedName['localSheetId'] != '') {
									// Local defined name
									// Switch on type
									switch ((string)$definedName['name']) {

										case '_xlnm._FilterDatabase':
										case '_xlnm.Print_Titles':
										case '_xlnm.Print_Area':
											break;

										default:
											$range = explode('!', (string)$definedName);
											if (count($range) == 2) {
												$range[0] = str_replace("''", "'", $range[0]);
												$range[0] = str_replace("'", "", $range[0]);
												if ($worksheet = $docSheet->getParent()->getSheetByName($range[0])) {
													$extractedRange = str_replace('$', '', $range[1]);
													$scope = $docSheet->getParent()->getSheet((string)$definedName['localSheetId']);

													$excel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $worksheet, $extractedRange, true, $scope) );
												}
											}
											break;
									}
								} else if (!isset($definedName['localSheetId'])) {
									// "Global" definedNames
									$locatedSheet = null;
									$extractedSheetName = '';
									if (strpos( (string)$definedName, '!' ) !== false) {
										// Extract sheet name
										$extractedSheetName = PHPExcel_Worksheet::extractSheetTitle( (string)$definedName, true );
										$extractedSheetName = $extractedSheetName[0];

										// Locate sheet
										$locatedSheet = $excel->getSheetByName($extractedSheetName);

										// Modify range
										$range = explode('!', $extractedRange);
										$extractedRange = isset($range[1]) ? $range[1] : $range[0];
									}

									if (!is_null($locatedSheet)) {
										$excel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $locatedSheet, $extractedRange, false) );
									}
								}
							}
						}
					}

					if (!$this->_readDataOnly) {
						// active sheet index
						$activeTab = intval($xmlWorkbook->bookViews->workbookView["activeTab"]); // refers to old sheet index

						// keep active sheet index if sheet is still loaded, else first sheet is set as the active
						if (isset($mapSheetId[$activeTab]) && $mapSheetId[$activeTab] !== null) {
							$excel->setActiveSheetIndex($mapSheetId[$activeTab]);
						} else {
							if ($excel->getSheetCount() == 0)
							{
								$excel->createSheet();
							}
							$excel->setActiveSheetIndex(0);
						}
					}
				break;
			}

		}

		$zip->close();

		return $excel;
	}

	private static function _readColor($color, $background=false) {
		if (isset($color["rgb"])) {
			return (string)$color["rgb"];
		} else if (isset($color["indexed"])) {
			return PHPExcel_Style_Color::indexedColor($color["indexed"],$background)->getARGB();
		} else if (isset($color["theme"])) {
			if (!is_null(self::$_theme)) {
				$returnColour = self::$_theme->getColourByIndex((int)$color["theme"]);
				if (isset($color["tint"])) {
					$tintAdjust = (float) $color["tint"];
					$returnColour = PHPExcel_Style_Color::changeBrightness($returnColour, $tintAdjust);
				}
				return 'FF'.$returnColour;
			}
		}

		if ($background) {
			return 'FFFFFFFF';
		}
		return 'FF000000';
	}

	private static function _readStyle($docStyle, $style) {
		// format code
		if (isset($style->numFmt)) {
			$docStyle->getNumberFormat()->setFormatCode($style->numFmt);
		}

		// font
		if (isset($style->font)) {
			$docStyle->getFont()->setName((string) $style->font->name["val"]);
			$docStyle->getFont()->setSize((string) $style->font->sz["val"]);
			if (isset($style->font->b)) {
				$docStyle->getFont()->setBold(!isset($style->font->b["val"]) || $style->font->b["val"] == 'true' || $style->font->b["val"] == '1');
			}
			if (isset($style->font->i)) {
				$docStyle->getFont()->setItalic(!isset($style->font->i["val"]) || $style->font->i["val"] == 'true' || $style->font->i["val"] == '1');
			}
			if (isset($style->font->strike)) {
				$docStyle->getFont()->setStrikethrough(!isset($style->font->strike["val"]) || $style->font->strike["val"] == 'true' || $style->font->strike["val"] == '1');
			}
			$docStyle->getFont()->getColor()->setARGB(self::_readColor($style->font->color));

			if (isset($style->font->u) && !isset($style->font->u["val"])) {
				$docStyle->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			} else if (isset($style->font->u) && isset($style->font->u["val"])) {
				$docStyle->getFont()->setUnderline((string)$style->font->u["val"]);
			}

			if (isset($style->font->vertAlign) && isset($style->font->vertAlign["val"])) {
				$vertAlign = strtolower((string)$style->font->vertAlign["val"]);
				if ($vertAlign == 'superscript') {
					$docStyle->getFont()->setSuperScript(true);
				}
				if ($vertAlign == 'subscript') {
					$docStyle->getFont()->setSubScript(true);
				}
			}
		}

		// fill
		if (isset($style->fill)) {
			if ($style->fill->gradientFill) {
				$gradientFill = $style->fill->gradientFill[0];
				if(!empty($gradientFill["type"])) {
					$docStyle->getFill()->setFillType((string) $gradientFill["type"]);
				}
				$docStyle->getFill()->setRotation(floatval($gradientFill["degree"]));
				$gradientFill->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
				$docStyle->getFill()->getStartColor()->setARGB(self::_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=0]"))->color) );
				$docStyle->getFill()->getEndColor()->setARGB(self::_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=1]"))->color) );
			} elseif ($style->fill->patternFill) {
				$patternType = (string)$style->fill->patternFill["patternType"] != '' ? (string)$style->fill->patternFill["patternType"] : 'solid';
				$docStyle->getFill()->setFillType($patternType);
				if ($style->fill->patternFill->fgColor) {
					$docStyle->getFill()->getStartColor()->setARGB(self::_readColor($style->fill->patternFill->fgColor,true));
				} else {
					$docStyle->getFill()->getStartColor()->setARGB('FF000000');
				}
				if ($style->fill->patternFill->bgColor) {
					$docStyle->getFill()->getEndColor()->setARGB(self::_readColor($style->fill->patternFill->bgColor,true));
				}
			}
		}

		// border
		if (isset($style->border)) {
			$diagonalUp   = false;
			$diagonalDown = false;
			if ($style->border["diagonalUp"] == 'true' || $style->border["diagonalUp"] == 1) {
				$diagonalUp = true;
			}
			if ($style->border["diagonalDown"] == 'true' || $style->border["diagonalDown"] == 1) {
				$diagonalDown = true;
			}
			if ($diagonalUp == false && $diagonalDown == false) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_NONE);
			} elseif ($diagonalUp == true && $diagonalDown == false) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_UP);
			} elseif ($diagonalUp == false && $diagonalDown == true) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_DOWN);
			} elseif ($diagonalUp == true && $diagonalDown == true) {
				$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_BOTH);
			}
			self::_readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
			self::_readBorder($docStyle->getBorders()->getRight(), $style->border->right);
			self::_readBorder($docStyle->getBorders()->getTop(), $style->border->top);
			self::_readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
			self::_readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
		}

		// alignment
		if (isset($style->alignment)) {
			$docStyle->getAlignment()->setHorizontal((string) $style->alignment["horizontal"]);
			$docStyle->getAlignment()->setVertical((string) $style->alignment["vertical"]);

			$textRotation = 0;
			if ((int)$style->alignment["textRotation"] <= 90) {
				$textRotation = (int)$style->alignment["textRotation"];
			} else if ((int)$style->alignment["textRotation"] > 90) {
				$textRotation = 90 - (int)$style->alignment["textRotation"];
			}

			$docStyle->getAlignment()->setTextRotation(intval($textRotation));
			$docStyle->getAlignment()->setWrapText( (string)$style->alignment["wrapText"] == "true" || (string)$style->alignment["wrapText"] == "1" );
			$docStyle->getAlignment()->setShrinkToFit( (string)$style->alignment["shrinkToFit"] == "true" || (string)$style->alignment["shrinkToFit"] == "1" );
			$docStyle->getAlignment()->setIndent( intval((string)$style->alignment["indent"]) > 0 ? intval((string)$style->alignment["indent"]) : 0 );
		}

		// protection
		if (isset($style->protection)) {
			if (isset($style->protection['locked'])) {
				if ((string)$style->protection['locked'] == 'true') {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}

			if (isset($style->protection['hidden'])) {
				if ((string)$style->protection['hidden'] == 'true') {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}
		}
	}

	private static function _readBorder($docBorder, $eleBorder) {
		if (isset($eleBorder["style"])) {
			$docBorder->setBorderStyle((string) $eleBorder["style"]);
		}
		if (isset($eleBorder->color)) {
			$docBorder->getColor()->setARGB(self::_readColor($eleBorder->color));
		}
	}

	private function _parseRichText($is = null) {
		$value = new PHPExcel_RichText();

		if (isset($is->t)) {
			$value->createText( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $is->t ) );
		} else {
			foreach ($is->r as $run) {
				if (!isset($run->rPr)) {
					$objText = $value->createText( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $run->t ) );

				} else {
					$objText = $value->createTextRun( PHPExcel_Shared_String::ControlCharacterOOXML2PHP( (string) $run->t ) );

					if (isset($run->rPr->rFont["val"])) {
						$objText->getFont()->setName((string) $run->rPr->rFont["val"]);
					}

					if (isset($run->rPr->sz["val"])) {
						$objText->getFont()->setSize((string) $run->rPr->sz["val"]);
					}

					if (isset($run->rPr->color)) {
						$objText->getFont()->setColor( new PHPExcel_Style_Color( self::_readColor($run->rPr->color) ) );
					}

					if ( (isset($run->rPr->b["val"]) && ((string) $run->rPr->b["val"] == 'true' || (string) $run->rPr->b["val"] == '1'))
					     || (isset($run->rPr->b) && !isset($run->rPr->b["val"])) ) {
						$objText->getFont()->setBold(true);
					}

					if ( (isset($run->rPr->i["val"]) && ((string) $run->rPr->i["val"] == 'true' || (string) $run->rPr->i["val"] == '1'))
					     || (isset($run->rPr->i) && !isset($run->rPr->i["val"])) ) {
						$objText->getFont()->setItalic(true);
					}

					if (isset($run->rPr->vertAlign) && isset($run->rPr->vertAlign["val"])) {
						$vertAlign = strtolower((string)$run->rPr->vertAlign["val"]);
						if ($vertAlign == 'superscript') {
							$objText->getFont()->setSuperScript(true);
						}
						if ($vertAlign == 'subscript') {
							$objText->getFont()->setSubScript(true);
						}
					}

					if (isset($run->rPr->u) && !isset($run->rPr->u["val"])) {
						$objText->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					} else if (isset($run->rPr->u) && isset($run->rPr->u["val"])) {
						$objText->getFont()->setUnderline((string)$run->rPr->u["val"]);
					}

					if ( (isset($run->rPr->strike["val"])  && ((string) $run->rPr->strike["val"] == 'true' || (string) $run->rPr->strike["val"] == '1'))
					     || (isset($run->rPr->strike) && !isset($run->rPr->strike["val"])) ) {
						$objText->getFont()->setStrikethrough(true);
					}
				}
			}
		}

		return $value;
	}

	private static function array_item($array, $key = 0) {
		return (isset($array[$key]) ? $array[$key] : null);
	}

	private static function dir_add($base, $add) {
		return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
	}

	private static function toCSSArray($style) {
		$style = str_replace(array("\r","\n"), "", $style);

		$temp = explode(';', $style);
		$style = array();
		foreach ($temp as $item) {
			$item = explode(':', $item);

			if (strpos($item[1], 'px') !== false) {
				$item[1] = str_replace('px', '', $item[1]);
			}
			if (strpos($item[1], 'pt') !== false) {
				$item[1] = str_replace('pt', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::fontSizeToPixels($item[1]);
			}
			if (strpos($item[1], 'in') !== false) {
				$item[1] = str_replace('in', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::inchSizeToPixels($item[1]);
			}
			if (strpos($item[1], 'cm') !== false) {
				$item[1] = str_replace('cm', '', $item[1]);
				$item[1] = PHPExcel_Shared_Font::centimeterSizeToPixels($item[1]);
			}

			$style[$item[0]] = $item[1];
		}

		return $style;
	}
}
