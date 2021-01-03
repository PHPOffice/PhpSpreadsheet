<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use SimpleXMLElement;

/**
 * Reader for SpreadsheetML, the XML schema for Microsoft Office Excel 2003.
 */
class Xml extends BaseReader
{
    /**
     * Formats.
     *
     * @var array
     */
    protected $styles = [];

    /**
     * Character set used in the file.
     *
     * @var string
     */
    protected $charSet = 'UTF-8';

    /**
     * Create a new Excel2003XML Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    /**
     * Can the current IReader read the file?
     *
     * @param string $pFilename
     *
     * @return bool
     */
    public function canRead($pFilename)
    {
        //    Office                    xmlns:o="urn:schemas-microsoft-com:office:office"
        //    Excel                    xmlns:x="urn:schemas-microsoft-com:office:excel"
        //    XML Spreadsheet            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        //    Spreadsheet component    xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
        //    XML schema                 xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
        //    XML data type            xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
        //    MS-persist recordset    xmlns:rs="urn:schemas-microsoft-com:rowset"
        //    Rowset                    xmlns:z="#RowsetSchema"
        //

        $signature = [
            '<?xml version="1.0"',
            '<?mso-application progid="Excel.Sheet"?>',
        ];

        // Open file
        $this->openFile($pFilename);
        $fileHandle = $this->fileHandle;

        // Read sample data (first 2 KB will do)
        $data = fread($fileHandle, 2048);
        fclose($fileHandle);
        $data = str_replace("'", '"', $data); // fix headers with single quote

        $valid = true;
        foreach ($signature as $match) {
            // every part of the signature must be present
            if (strpos($data, $match) === false) {
                $valid = false;

                break;
            }
        }

        //    Retrieve charset encoding
        if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/um', $data, $matches)) {
            $this->charSet = strtoupper($matches[1]);
        }

        return $valid;
    }

    /**
     * Check if the file is a valid SimpleXML.
     *
     * @param string $pFilename
     *
     * @return false|SimpleXMLElement
     */
    public function trySimpleXMLLoadString($pFilename)
    {
        try {
            $xml = simplexml_load_string(
                $this->securityScanner->scan(file_get_contents($pFilename)),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );
        } catch (\Exception $e) {
            throw new Exception('Cannot load invalid XML file: ' . $pFilename, 0, $e);
        }

        return $xml;
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object.
     *
     * @param string $pFilename
     *
     * @return array
     */
    public function listWorksheetNames($pFilename)
    {
        File::assertFile($pFilename);
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }

        $worksheetNames = [];

        $xml = $this->trySimpleXMLLoadString($pFilename);

        $namespaces = $xml->getNamespaces(true);

        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);
            $worksheetNames[] = self::convertStringEncoding((string) $worksheet_ss['Name'], $this->charSet);
        }

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $pFilename
     *
     * @return array
     */
    public function listWorksheetInfo($pFilename)
    {
        File::assertFile($pFilename);

        $worksheetInfo = [];

        $xml = $this->trySimpleXMLLoadString($pFilename);

        $namespaces = $xml->getNamespaces(true);

        $worksheetID = 1;
        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);

            $tmpInfo = [];
            $tmpInfo['worksheetName'] = '';
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;

            if (isset($worksheet_ss['Name'])) {
                $tmpInfo['worksheetName'] = (string) $worksheet_ss['Name'];
            } else {
                $tmpInfo['worksheetName'] = "Worksheet_{$worksheetID}";
            }

            if (isset($worksheet->Table->Row)) {
                $rowIndex = 0;

                foreach ($worksheet->Table->Row as $rowData) {
                    $columnIndex = 0;
                    $rowHasData = false;

                    foreach ($rowData->Cell as $cell) {
                        if (isset($cell->Data)) {
                            $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                            $rowHasData = true;
                        }

                        ++$columnIndex;
                    }

                    ++$rowIndex;

                    if ($rowHasData) {
                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                    }
                }
            }

            $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

            $worksheetInfo[] = $tmpInfo;
            ++$worksheetID;
        }

        return $worksheetInfo;
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @param string $pFilename
     *
     * @return Spreadsheet
     */
    public function load($pFilename)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    private static function identifyFixedStyleValue($styleList, &$styleAttributeValue)
    {
        $styleAttributeValue = strtolower($styleAttributeValue);
        foreach ($styleList as $style) {
            if ($styleAttributeValue == strtolower($style)) {
                $styleAttributeValue = $style;

                return true;
            }
        }

        return false;
    }

    /**
     * pixel units to excel width units(units of 1/256th of a character width).
     *
     * @param float $pxs
     *
     * @return float
     */
    protected static function pixel2WidthUnits($pxs)
    {
        $UNIT_OFFSET_MAP = [0, 36, 73, 109, 146, 182, 219];

        $widthUnits = 256 * ($pxs / 7);
        $widthUnits += $UNIT_OFFSET_MAP[($pxs % 7)];

        return $widthUnits;
    }

    /**
     * excel width units(units of 1/256th of a character width) to pixel units.
     *
     * @param float $widthUnits
     *
     * @return float
     */
    protected static function widthUnits2Pixel($widthUnits)
    {
        $pixels = ($widthUnits / 256) * 7;
        $offsetWidthUnits = $widthUnits % 256;

        return $pixels + round($offsetWidthUnits / (256 / 7));
    }

    protected static function hex2str($hex)
    {
        return chr(hexdec($hex[1]));
    }

    /**
     * Loads from file into Spreadsheet instance.
     *
     * @param string $pFilename
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        File::assertFile($pFilename);
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }

        $xml = $this->trySimpleXMLLoadString($pFilename);

        $namespaces = $xml->getNamespaces(true);

        $docProps = $spreadsheet->getProperties();
        if (isset($xml->DocumentProperties[0])) {
            foreach ($xml->DocumentProperties[0] as $propertyName => $propertyValue) {
                switch ($propertyName) {
                    case 'Title':
                        $docProps->setTitle(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Subject':
                        $docProps->setSubject(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Author':
                        $docProps->setCreator(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Created':
                        $creationDate = strtotime($propertyValue);
                        $docProps->setCreated($creationDate);

                        break;
                    case 'LastAuthor':
                        $docProps->setLastModifiedBy(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'LastSaved':
                        $lastSaveDate = strtotime($propertyValue);
                        $docProps->setModified($lastSaveDate);

                        break;
                    case 'Company':
                        $docProps->setCompany(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Category':
                        $docProps->setCategory(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Manager':
                        $docProps->setManager(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Keywords':
                        $docProps->setKeywords(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                    case 'Description':
                        $docProps->setDescription(self::convertStringEncoding($propertyValue, $this->charSet));

                        break;
                }
            }
        }
        if (isset($xml->CustomDocumentProperties)) {
            foreach ($xml->CustomDocumentProperties[0] as $propertyName => $propertyValue) {
                $propertyAttributes = $propertyValue->attributes($namespaces['dt']);
                $propertyName = preg_replace_callback('/_x([0-9a-z]{4})_/', ['self', 'hex2str'], $propertyName);
                $propertyType = Properties::PROPERTY_TYPE_UNKNOWN;
                switch ((string) $propertyAttributes) {
                    case 'string':
                        $propertyType = Properties::PROPERTY_TYPE_STRING;
                        $propertyValue = trim($propertyValue);

                        break;
                    case 'boolean':
                        $propertyType = Properties::PROPERTY_TYPE_BOOLEAN;
                        $propertyValue = (bool) $propertyValue;

                        break;
                    case 'integer':
                        $propertyType = Properties::PROPERTY_TYPE_INTEGER;
                        $propertyValue = (int) $propertyValue;

                        break;
                    case 'float':
                        $propertyType = Properties::PROPERTY_TYPE_FLOAT;
                        $propertyValue = (float) $propertyValue;

                        break;
                    case 'dateTime.tz':
                        $propertyType = Properties::PROPERTY_TYPE_DATE;
                        $propertyValue = strtotime(trim($propertyValue));

                        break;
                }
                $docProps->setCustomProperty($propertyName, $propertyValue, $propertyType);
            }
        }

        $this->parseStyles($xml, $namespaces);

        $worksheetID = 0;
        $xml_ss = $xml->children($namespaces['ss']);

        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);

            if ((isset($this->loadSheetsOnly)) && (isset($worksheet_ss['Name'])) &&
                (!in_array($worksheet_ss['Name'], $this->loadSheetsOnly))) {
                continue;
            }

            // Create new Worksheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($worksheetID);
            if (isset($worksheet_ss['Name'])) {
                $worksheetName = self::convertStringEncoding((string) $worksheet_ss['Name'], $this->charSet);
                //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                //        formula cells... during the load, all formulae should be correct, and we're simply bringing
                //        the worksheet name in line with the formula, not the reverse
                $spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);
            }

            $columnID = 'A';
            if (isset($worksheet->Table->Column)) {
                foreach ($worksheet->Table->Column as $columnData) {
                    $columnData_ss = $columnData->attributes($namespaces['ss']);
                    if (isset($columnData_ss['Index'])) {
                        $columnID = Coordinate::stringFromColumnIndex((int) $columnData_ss['Index']);
                    }
                    if (isset($columnData_ss['Width'])) {
                        $columnWidth = $columnData_ss['Width'];
                        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth($columnWidth / 5.4);
                    }
                    ++$columnID;
                }
            }

            $rowID = 1;
            if (isset($worksheet->Table->Row)) {
                $additionalMergedCells = 0;
                foreach ($worksheet->Table->Row as $rowData) {
                    $rowHasData = false;
                    $row_ss = $rowData->attributes($namespaces['ss']);
                    if (isset($row_ss['Index'])) {
                        $rowID = (int) $row_ss['Index'];
                    }

                    $columnID = 'A';
                    foreach ($rowData->Cell as $cell) {
                        $cell_ss = $cell->attributes($namespaces['ss']);
                        if (isset($cell_ss['Index'])) {
                            $columnID = Coordinate::stringFromColumnIndex((int) $cell_ss['Index']);
                        }
                        $cellRange = $columnID . $rowID;

                        if ($this->getReadFilter() !== null) {
                            if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                ++$columnID;

                                continue;
                            }
                        }

                        if (isset($cell_ss['HRef'])) {
                            $spreadsheet->getActiveSheet()->getCell($cellRange)->getHyperlink()->setUrl($cell_ss['HRef']);
                        }

                        if ((isset($cell_ss['MergeAcross'])) || (isset($cell_ss['MergeDown']))) {
                            $columnTo = $columnID;
                            if (isset($cell_ss['MergeAcross'])) {
                                $additionalMergedCells += (int) $cell_ss['MergeAcross'];
                                $columnTo = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($columnID) + $cell_ss['MergeAcross']);
                            }
                            $rowTo = $rowID;
                            if (isset($cell_ss['MergeDown'])) {
                                $rowTo = $rowTo + $cell_ss['MergeDown'];
                            }
                            $cellRange .= ':' . $columnTo . $rowTo;
                            $spreadsheet->getActiveSheet()->mergeCells($cellRange);
                        }

                        $cellIsSet = $hasCalculatedValue = false;
                        $cellDataFormula = '';
                        if (isset($cell_ss['Formula'])) {
                            $cellDataFormula = $cell_ss['Formula'];
                            $hasCalculatedValue = true;
                        }
                        if (isset($cell->Data)) {
                            $cellValue = $cellData = $cell->Data;
                            $type = DataType::TYPE_NULL;
                            $cellData_ss = $cellData->attributes($namespaces['ss']);
                            if (isset($cellData_ss['Type'])) {
                                $cellDataType = $cellData_ss['Type'];
                                switch ($cellDataType) {
                                    /*
                                    const TYPE_STRING        = 's';
                                    const TYPE_FORMULA        = 'f';
                                    const TYPE_NUMERIC        = 'n';
                                    const TYPE_BOOL            = 'b';
                                    const TYPE_NULL            = 'null';
                                    const TYPE_INLINE        = 'inlineStr';
                                    const TYPE_ERROR        = 'e';
                                    */
                                    case 'String':
                                        $cellValue = self::convertStringEncoding($cellValue, $this->charSet);
                                        $type = DataType::TYPE_STRING;

                                        break;
                                    case 'Number':
                                        $type = DataType::TYPE_NUMERIC;
                                        $cellValue = (float) $cellValue;
                                        if (floor($cellValue) == $cellValue) {
                                            $cellValue = (int) $cellValue;
                                        }

                                        break;
                                    case 'Boolean':
                                        $type = DataType::TYPE_BOOL;
                                        $cellValue = ($cellValue != 0);

                                        break;
                                    case 'DateTime':
                                        $type = DataType::TYPE_NUMERIC;
                                        $cellValue = Date::PHPToExcel(strtotime($cellValue));

                                        break;
                                    case 'Error':
                                        $type = DataType::TYPE_ERROR;

                                        break;
                                }
                            }

                            if ($hasCalculatedValue) {
                                $type = DataType::TYPE_FORMULA;
                                $columnNumber = Coordinate::columnIndexFromString($columnID);
                                if (substr($cellDataFormula, 0, 3) == 'of:') {
                                    $cellDataFormula = substr($cellDataFormula, 3);
                                    $temp = explode('"', $cellDataFormula);
                                    $key = false;
                                    foreach ($temp as &$value) {
                                        //    Only replace in alternate array entries (i.e. non-quoted blocks)
                                        if ($key = !$key) {
                                            $value = str_replace(['[.', '.', ']'], '', $value);
                                        }
                                    }
                                } else {
                                    //    Convert R1C1 style references to A1 style references (but only when not quoted)
                                    $temp = explode('"', $cellDataFormula);
                                    $key = false;
                                    foreach ($temp as &$value) {
                                        //    Only replace in alternate array entries (i.e. non-quoted blocks)
                                        if ($key = !$key) {
                                            preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                                            //    Reverse the matches array, otherwise all our offsets will become incorrect if we modify our way
                                            //        through the formula from left to right. Reversing means that we work right to left.through
                                            //        the formula
                                            $cellReferences = array_reverse($cellReferences);
                                            //    Loop through each R1C1 style reference in turn, converting it to its A1 style equivalent,
                                            //        then modify the formula to use that new reference
                                            foreach ($cellReferences as $cellReference) {
                                                $rowReference = $cellReference[2][0];
                                                //    Empty R reference is the current row
                                                if ($rowReference == '') {
                                                    $rowReference = $rowID;
                                                }
                                                //    Bracketed R references are relative to the current row
                                                if ($rowReference[0] == '[') {
                                                    $rowReference = $rowID + trim($rowReference, '[]');
                                                }
                                                $columnReference = $cellReference[4][0];
                                                //    Empty C reference is the current column
                                                if ($columnReference == '') {
                                                    $columnReference = $columnNumber;
                                                }
                                                //    Bracketed C references are relative to the current column
                                                if ($columnReference[0] == '[') {
                                                    $columnReference = $columnNumber + trim($columnReference, '[]');
                                                }
                                                $A1CellReference = Coordinate::stringFromColumnIndex($columnReference) . $rowReference;
                                                $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                                            }
                                        }
                                    }
                                }
                                unset($value);
                                //    Then rebuild the formula string
                                $cellDataFormula = implode('"', $temp);
                            }

                            $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $cellValue), $type);
                            if ($hasCalculatedValue) {
                                $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setCalculatedValue($cellValue);
                            }
                            $cellIsSet = $rowHasData = true;
                        }

                        if (isset($cell->Comment)) {
                            $commentAttributes = $cell->Comment->attributes($namespaces['ss']);
                            $author = 'unknown';
                            if (isset($commentAttributes->Author)) {
                                $author = (string) $commentAttributes->Author;
                            }
                            $node = $cell->Comment->Data->asXML();
                            $annotation = strip_tags($node);
                            $spreadsheet->getActiveSheet()->getComment($columnID . $rowID)->setAuthor(self::convertStringEncoding($author, $this->charSet))->setText($this->parseRichText($annotation));
                        }

                        if (($cellIsSet) && (isset($cell_ss['StyleID']))) {
                            $style = (string) $cell_ss['StyleID'];
                            if ((isset($this->styles[$style])) && (!empty($this->styles[$style]))) {
                                if (!$spreadsheet->getActiveSheet()->cellExists($columnID . $rowID)) {
                                    $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValue(null);
                                }
                                $spreadsheet->getActiveSheet()->getStyle($cellRange)->applyFromArray($this->styles[$style]);
                            }
                        }
                        ++$columnID;
                        while ($additionalMergedCells > 0) {
                            ++$columnID;
                            --$additionalMergedCells;
                        }
                    }

                    if ($rowHasData) {
                        if (isset($row_ss['Height'])) {
                            $rowHeight = $row_ss['Height'];
                            $spreadsheet->getActiveSheet()->getRowDimension($rowID)->setRowHeight($rowHeight);
                        }
                    }

                    ++$rowID;
                }
            }
            ++$worksheetID;
        }

        // Return
        return $spreadsheet;
    }

    protected static function convertStringEncoding($string, $charset)
    {
        if ($charset != 'UTF-8') {
            return StringHelper::convertEncoding($string, 'UTF-8', $charset);
        }

        return $string;
    }

    protected function parseRichText($is)
    {
        $value = new RichText();

        $value->createText(self::convertStringEncoding($is, $this->charSet));

        return $value;
    }

    private function parseStyles(SimpleXMLElement $xml, array $namespaces): void
    {
        if (!isset($xml->Styles)) {
            return;
        }

        foreach ($xml->Styles[0] as $style) {
            $style_ss = $style->attributes($namespaces['ss']);
            $styleID = (string) $style_ss['ID'];
            $this->styles[$styleID] = (isset($this->styles['Default'])) ? $this->styles['Default'] : [];
            foreach ($style as $styleType => $styleData) {
                $styleAttributes = $styleData->attributes($namespaces['ss']);
                switch ($styleType) {
                    case 'Alignment':
                        $this->parseStyleAlignment($styleID, $styleAttributes);

                        break;
                    case 'Borders':
                        $this->parseStyleBorders($styleID, $styleData, $namespaces);

                        break;
                    case 'Font':
                        $this->parseStyleFont($styleID, $styleAttributes);

                        break;
                    case 'Interior':
                        $this->parseStyleInterior($styleID, $styleAttributes);

                        break;
                    case 'NumberFormat':
                        $this->parseStyleNumberFormat($styleID, $styleAttributes);

                        break;
                }
            }
        }
    }

    /**
     * @param string $styleID
     */
    private function parseStyleAlignment($styleID, SimpleXMLElement $styleAttributes): void
    {
        $verticalAlignmentStyles = [
            Alignment::VERTICAL_BOTTOM,
            Alignment::VERTICAL_TOP,
            Alignment::VERTICAL_CENTER,
            Alignment::VERTICAL_JUSTIFY,
        ];
        $horizontalAlignmentStyles = [
            Alignment::HORIZONTAL_GENERAL,
            Alignment::HORIZONTAL_LEFT,
            Alignment::HORIZONTAL_RIGHT,
            Alignment::HORIZONTAL_CENTER,
            Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            Alignment::HORIZONTAL_JUSTIFY,
        ];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'Vertical':
                    if (self::identifyFixedStyleValue($verticalAlignmentStyles, $styleAttributeValue)) {
                        $this->styles[$styleID]['alignment']['vertical'] = $styleAttributeValue;
                    }

                    break;
                case 'Horizontal':
                    if (self::identifyFixedStyleValue($horizontalAlignmentStyles, $styleAttributeValue)) {
                        $this->styles[$styleID]['alignment']['horizontal'] = $styleAttributeValue;
                    }

                    break;
                case 'WrapText':
                    $this->styles[$styleID]['alignment']['wrapText'] = true;

                    break;
            }
        }
    }

    /**
     * @param $styleID
     */
    private function parseStyleBorders($styleID, SimpleXMLElement $styleData, array $namespaces): void
    {
        foreach ($styleData->Border as $borderStyle) {
            $borderAttributes = $borderStyle->attributes($namespaces['ss']);
            $thisBorder = [];
            foreach ($borderAttributes as $borderStyleKey => $borderStyleValue) {
                switch ($borderStyleKey) {
                    case 'LineStyle':
                        $thisBorder['borderStyle'] = Border::BORDER_MEDIUM;

                        break;
                    case 'Weight':
                        break;
                    case 'Position':
                        $borderPosition = strtolower($borderStyleValue);

                        break;
                    case 'Color':
                        $borderColour = substr($borderStyleValue, 1);
                        $thisBorder['color']['rgb'] = $borderColour;

                        break;
                }
            }
            if (!empty($thisBorder)) {
                if (($borderPosition == 'left') || ($borderPosition == 'right') || ($borderPosition == 'top') || ($borderPosition == 'bottom')) {
                    $this->styles[$styleID]['borders'][$borderPosition] = $thisBorder;
                }
            }
        }
    }

    /**
     * @param $styleID
     */
    private function parseStyleFont($styleID, SimpleXMLElement $styleAttributes): void
    {
        $underlineStyles = [
            Font::UNDERLINE_NONE,
            Font::UNDERLINE_DOUBLE,
            Font::UNDERLINE_DOUBLEACCOUNTING,
            Font::UNDERLINE_SINGLE,
            Font::UNDERLINE_SINGLEACCOUNTING,
        ];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'FontName':
                    $this->styles[$styleID]['font']['name'] = $styleAttributeValue;

                    break;
                case 'Size':
                    $this->styles[$styleID]['font']['size'] = $styleAttributeValue;

                    break;
                case 'Color':
                    $this->styles[$styleID]['font']['color']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Bold':
                    $this->styles[$styleID]['font']['bold'] = true;

                    break;
                case 'Italic':
                    $this->styles[$styleID]['font']['italic'] = true;

                    break;
                case 'Underline':
                    if (self::identifyFixedStyleValue($underlineStyles, $styleAttributeValue)) {
                        $this->styles[$styleID]['font']['underline'] = $styleAttributeValue;
                    }

                    break;
            }
        }
    }

    /**
     * @param $styleID
     */
    private function parseStyleInterior($styleID, SimpleXMLElement $styleAttributes): void
    {
        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            switch ($styleAttributeKey) {
                case 'Color':
                    $this->styles[$styleID]['fill']['color']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Pattern':
                    $this->styles[$styleID]['fill']['fillType'] = strtolower($styleAttributeValue);

                    break;
            }
        }
    }

    /**
     * @param $styleID
     */
    private function parseStyleNumberFormat($styleID, SimpleXMLElement $styleAttributes): void
    {
        $fromFormats = ['\-', '\ '];
        $toFormats = ['-', ' '];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = str_replace($fromFormats, $toFormats, $styleAttributeValue);
            switch ($styleAttributeValue) {
                case 'Short Date':
                    $styleAttributeValue = 'dd/mm/yyyy';

                    break;
            }

            if ($styleAttributeValue > '') {
                $this->styles[$styleID]['numberFormat']['formatCode'] = $styleAttributeValue;
            }
        }
    }
}
