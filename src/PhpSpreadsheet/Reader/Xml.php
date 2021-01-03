<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xml\PageSettings;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
     * Create a new Excel2003XML Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    private $fileContents = '';

    private static $mappings = [
        'borderStyle' => [
            '1continuous' => Border::BORDER_THIN,
            '1dash' => Border::BORDER_DASHED,
            '1dashdot' => Border::BORDER_DASHDOT,
            '1dashdotdot' => Border::BORDER_DASHDOTDOT,
            '1dot' => Border::BORDER_DOTTED,
            '1double' => Border::BORDER_DOUBLE,
            '2continuous' => Border::BORDER_MEDIUM,
            '2dash' => Border::BORDER_MEDIUMDASHED,
            '2dashdot' => Border::BORDER_MEDIUMDASHDOT,
            '2dashdotdot' => Border::BORDER_MEDIUMDASHDOTDOT,
            '2dot' => Border::BORDER_DOTTED,
            '2double' => Border::BORDER_DOUBLE,
            '3continuous' => Border::BORDER_THICK,
            '3dash' => Border::BORDER_MEDIUMDASHED,
            '3dashdot' => Border::BORDER_MEDIUMDASHDOT,
            '3dashdotdot' => Border::BORDER_MEDIUMDASHDOTDOT,
            '3dot' => Border::BORDER_DOTTED,
            '3double' => Border::BORDER_DOUBLE,
        ],
        'fillType' => [
            'solid' => Fill::FILL_SOLID,
            'gray75' => Fill::FILL_PATTERN_DARKGRAY,
            'gray50' => Fill::FILL_PATTERN_MEDIUMGRAY,
            'gray25' => Fill::FILL_PATTERN_LIGHTGRAY,
            'gray125' => Fill::FILL_PATTERN_GRAY125,
            'gray0625' => Fill::FILL_PATTERN_GRAY0625,
            'horzstripe' => Fill::FILL_PATTERN_DARKHORIZONTAL, // horizontal stripe
            'vertstripe' => Fill::FILL_PATTERN_DARKVERTICAL, // vertical stripe
            'reversediagstripe' => Fill::FILL_PATTERN_DARKUP, // reverse diagonal stripe
            'diagstripe' => Fill::FILL_PATTERN_DARKDOWN, // diagonal stripe
            'diagcross' => Fill::FILL_PATTERN_DARKGRID, // diagoanl crosshatch
            'thickdiagcross' => Fill::FILL_PATTERN_DARKTRELLIS, // thick diagonal crosshatch
            'thinhorzstripe' => Fill::FILL_PATTERN_LIGHTHORIZONTAL,
            'thinvertstripe' => Fill::FILL_PATTERN_LIGHTVERTICAL,
            'thinreversediagstripe' => Fill::FILL_PATTERN_LIGHTUP,
            'thindiagstripe' => Fill::FILL_PATTERN_LIGHTDOWN,
            'thinhorzcross' => Fill::FILL_PATTERN_LIGHTGRID, // thin horizontal crosshatch
            'thindiagcross' => Fill::FILL_PATTERN_LIGHTTRELLIS, // thin diagonal crosshatch
        ],
    ];

    public static function xmlMappings(): array
    {
        return self::$mappings;
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
        $data = file_get_contents($pFilename);

        // Why?
        //$data = str_replace("'", '"', $data); // fix headers with single quote

        $valid = true;
        foreach ($signature as $match) {
            // every part of the signature must be present
            if (strpos($data, $match) === false) {
                $valid = false;

                break;
            }
        }

        //    Retrieve charset encoding
        if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $data, $matches)) {
            $charSet = strtoupper($matches[1]);
            if (1 == preg_match('/^ISO-8859-\d[\dL]?$/i', $charSet)) {
                $data = StringHelper::convertEncoding($data, 'UTF-8', $charSet);
                $data = preg_replace('/(<?xml.*encoding=[\'"]).*?([\'"].*?>)/um', '$1' . 'UTF-8' . '$2', $data, 1);
            }
        }
        $this->fileContents = $data;

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
                $this->securityScanner->scan($this->fileContents ?: file_get_contents($pFilename)),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );
        } catch (\Exception $e) {
            throw new Exception('Cannot load invalid XML file: ' . $pFilename, 0, $e);
        }
        $this->fileContents = '';

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
            $worksheetNames[] = (string) $worksheet_ss['Name'];
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
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }

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

            $tmpInfo['worksheetName'] = "Worksheet_{$worksheetID}";
            if (isset($worksheet_ss['Name'])) {
                $tmpInfo['worksheetName'] = (string) $worksheet_ss['Name'];
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
        $returnValue = false;
        $styleAttributeValue = strtolower($styleAttributeValue);
        foreach ($styleList as $style) {
            if ($styleAttributeValue == strtolower($style)) {
                $styleAttributeValue = $style;
                $returnValue = true;

                break;
            }
        }

        return $returnValue;
    }

    protected static function hex2str($hex)
    {
        return mb_chr((int) hexdec($hex[1]), 'UTF-8');
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
                $stringValue = (string) $propertyValue;
                switch ($propertyName) {
                    case 'Title':
                        $docProps->setTitle($stringValue);

                        break;
                    case 'Subject':
                        $docProps->setSubject($stringValue);

                        break;
                    case 'Author':
                        $docProps->setCreator($stringValue);

                        break;
                    case 'Created':
                        $creationDate = strtotime($stringValue);
                        $docProps->setCreated($creationDate);

                        break;
                    case 'LastAuthor':
                        $docProps->setLastModifiedBy($stringValue);

                        break;
                    case 'LastSaved':
                        $lastSaveDate = strtotime($stringValue);
                        $docProps->setModified($lastSaveDate);

                        break;
                    case 'Company':
                        $docProps->setCompany($stringValue);

                        break;
                    case 'Category':
                        $docProps->setCategory($stringValue);

                        break;
                    case 'Manager':
                        $docProps->setManager($stringValue);

                        break;
                    case 'Keywords':
                        $docProps->setKeywords($stringValue);

                        break;
                    case 'Description':
                        $docProps->setDescription($stringValue);

                        break;
                }
            }
        }
        if (isset($xml->CustomDocumentProperties)) {
            foreach ($xml->CustomDocumentProperties[0] as $propertyName => $propertyValue) {
                $propertyAttributes = $propertyValue->attributes($namespaces['dt']);
                $propertyName = preg_replace_callback('/_x([0-9a-f]{4})_/i', ['self', 'hex2str'], $propertyName);
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

            if (
                (isset($this->loadSheetsOnly)) && (isset($worksheet_ss['Name'])) &&
                (!in_array($worksheet_ss['Name'], $this->loadSheetsOnly))
            ) {
                continue;
            }

            // Create new Worksheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($worksheetID);
            if (isset($worksheet_ss['Name'])) {
                $worksheetName = (string) $worksheet_ss['Name'];
                //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                //        formula cells... during the load, all formulae should be correct, and we're simply bringing
                //        the worksheet name in line with the formula, not the reverse
                $spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);
            }

            // locally scoped defined names
            if (isset($worksheet->Names[0])) {
                foreach ($worksheet->Names[0] as $definedName) {
                    $definedName_ss = $definedName->attributes($namespaces['ss']);
                    $name = (string) $definedName_ss['Name'];
                    $definedValue = (string) $definedName_ss['RefersTo'];
                    $convertedValue = AddressHelper::convertFormulaToA1($definedValue);
                    if ($convertedValue[0] === '=') {
                        $convertedValue = substr($convertedValue, 1);
                    }
                    $spreadsheet->addDefinedName(DefinedName::createInstance($name, $spreadsheet->getActiveSheet(), $convertedValue, true));
                }
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
                            $spreadsheet->getActiveSheet()->getCell($cellRange)->getHyperlink()->setUrl((string) $cell_ss['HRef']);
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

                        $hasCalculatedValue = false;
                        $cellDataFormula = '';
                        if (isset($cell_ss['Formula'])) {
                            $cellDataFormula = $cell_ss['Formula'];
                            $hasCalculatedValue = true;
                        }
                        if (isset($cell->Data)) {
                            $cellData = $cell->Data;
                            $cellValue = (string) $cellData;
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
                                        $cellValue = Date::PHPToExcel(strtotime($cellValue . ' UTC'));

                                        break;
                                    case 'Error':
                                        $type = DataType::TYPE_ERROR;
                                        $hasCalculatedValue = false;

                                        break;
                                }
                            }

                            if ($hasCalculatedValue) {
                                $type = DataType::TYPE_FORMULA;
                                $columnNumber = Coordinate::columnIndexFromString($columnID);
                                $cellDataFormula = AddressHelper::convertFormulaToA1($cellDataFormula, $rowID, $columnNumber);
                            }

                            $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $cellValue), $type);
                            if ($hasCalculatedValue) {
                                $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setCalculatedValue($cellValue);
                            }
                            $rowHasData = true;
                        }

                        if (isset($cell->Comment)) {
                            $commentAttributes = $cell->Comment->attributes($namespaces['ss']);
                            $author = 'unknown';
                            if (isset($commentAttributes->Author)) {
                                $author = (string) $commentAttributes->Author;
                            }
                            $node = $cell->Comment->Data->asXML();
                            $annotation = strip_tags($node);
                            $spreadsheet->getActiveSheet()->getComment($columnID . $rowID)->setAuthor($author)->setText($this->parseRichText($annotation));
                        }

                        if (isset($cell_ss['StyleID'])) {
                            $style = (string) $cell_ss['StyleID'];
                            if ((isset($this->styles[$style])) && (!empty($this->styles[$style]))) {
                                //if (!$spreadsheet->getActiveSheet()->cellExists($columnID . $rowID)) {
                                //    $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValue(null);
                                //}
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

                $xmlX = $worksheet->children($namespaces['x']);
                if (isset($xmlX->WorksheetOptions)) {
                    (new PageSettings($xmlX, $namespaces))->loadPageSettings($spreadsheet);
                }
            }
            ++$worksheetID;
        }

        // Globally scoped defined names
        $activeWorksheet = $spreadsheet->setActiveSheetIndex(0);
        if (isset($xml->Names[0])) {
            foreach ($xml->Names[0] as $definedName) {
                $definedName_ss = $definedName->attributes($namespaces['ss']);
                $name = (string) $definedName_ss['Name'];
                $definedValue = (string) $definedName_ss['RefersTo'];
                $convertedValue = AddressHelper::convertFormulaToA1($definedValue);
                if ($convertedValue[0] === '=') {
                    $convertedValue = substr($convertedValue, 1);
                }
                $spreadsheet->addDefinedName(DefinedName::createInstance($name, $activeWorksheet, $convertedValue));
            }
        }

        // Return
        return $spreadsheet;
    }

    protected function parseRichText($is)
    {
        $value = new RichText();

        $value->createText($is);

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
                case 'Rotate':
                    $this->styles[$styleID]['alignment']['textRotation'] = $styleAttributeValue;

                    break;
            }
        }
    }

    private static $borderPositions = ['top', 'left', 'bottom', 'right'];

    /**
     * @param $styleID
     */
    private function parseStyleBorders($styleID, SimpleXMLElement $styleData, array $namespaces): void
    {
        $diagonalDirection = '';
        $borderPosition = '';
        foreach ($styleData->Border as $borderStyle) {
            $borderAttributes = $borderStyle->attributes($namespaces['ss']);
            $thisBorder = [];
            $style = (string) $borderAttributes->Weight;
            $style .= strtolower((string) $borderAttributes->LineStyle);
            $thisBorder['borderStyle'] = self::$mappings['borderStyle'][$style] ?? Border::BORDER_NONE;
            foreach ($borderAttributes as $borderStyleKey => $borderStyleValue) {
                switch ($borderStyleKey) {
                    case 'Position':
                        $borderStyleValue = strtolower((string) $borderStyleValue);
                        if (in_array($borderStyleValue, self::$borderPositions)) {
                            $borderPosition = $borderStyleValue;
                        } elseif ($borderStyleValue == 'diagonalleft') {
                            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_DOWN;
                        } elseif ($borderStyleValue == 'diagonalright') {
                            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_UP;
                        }

                        break;
                    case 'Color':
                        $borderColour = substr($borderStyleValue, 1);
                        $thisBorder['color']['rgb'] = $borderColour;

                        break;
                }
            }
            if ($borderPosition) {
                $this->styles[$styleID]['borders'][$borderPosition] = $thisBorder;
            } elseif ($diagonalDirection) {
                $this->styles[$styleID]['borders']['diagonalDirection'] = $diagonalDirection;
                $this->styles[$styleID]['borders']['diagonal'] = $thisBorder;
            }
        }
    }

    private static $underlineStyles = [
        Font::UNDERLINE_NONE,
        Font::UNDERLINE_DOUBLE,
        Font::UNDERLINE_DOUBLEACCOUNTING,
        Font::UNDERLINE_SINGLE,
        Font::UNDERLINE_SINGLEACCOUNTING,
    ];

    private function parseStyleFontUnderline(string $styleID, string $styleAttributeValue): void
    {
        if (self::identifyFixedStyleValue(self::$underlineStyles, $styleAttributeValue)) {
            $this->styles[$styleID]['font']['underline'] = $styleAttributeValue;
        }
    }

    private function parseStyleFontVerticalAlign(string $styleID, string $styleAttributeValue): void
    {
        if ($styleAttributeValue == 'Superscript') {
            $this->styles[$styleID]['font']['superscript'] = true;
        }
        if ($styleAttributeValue == 'Subscript') {
            $this->styles[$styleID]['font']['subscript'] = true;
        }
    }

    /**
     * @param $styleID
     */
    private function parseStyleFont(string $styleID, SimpleXMLElement $styleAttributes): void
    {
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
                    $this->parseStyleFontUnderline($styleID, $styleAttributeValue);

                    break;
                case 'VerticalAlign':
                    $this->parseStyleFontVerticalAlign($styleID, $styleAttributeValue);

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
                    $this->styles[$styleID]['fill']['endColor']['rgb'] = substr($styleAttributeValue, 1);
                    $this->styles[$styleID]['fill']['startColor']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'PatternColor':
                    $this->styles[$styleID]['fill']['startColor']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Pattern':
                    $lcStyleAttributeValue = strtolower((string) $styleAttributeValue);
                    $this->styles[$styleID]['fill']['fillType'] = self::$mappings['fillType'][$lcStyleAttributeValue] ?? Fill::FILL_NONE;

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
