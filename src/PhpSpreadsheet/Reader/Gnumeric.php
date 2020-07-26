<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric\PageSetup;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use XMLReader;

class Gnumeric extends BaseReader
{
    private const UOM_CONVERSION_POINTS_TO_CENTIMETERS = 0.03527777778;

    /**
     * Shared Expressions.
     *
     * @var array
     */
    private $expressions = [];

    /**
     * Spreadsheet shared across all functions.
     *
     * @var Spreadsheet
     */
    private $spreadsheet;

    private $referenceHelper;

    /**
     * Namespace shared across all functions.
     * It is 'gnm', except for really old sheets which use 'gmr'.
     *
     * @var string
     */
    private $gnm = 'gnm';

    /**
     * Create a new Gnumeric.
     */
    public function __construct()
    {
        parent::__construct();
        $this->referenceHelper = ReferenceHelper::getInstance();
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
        File::assertFile($pFilename);

        // Check if gzlib functions are available
        $data = '';
        if (function_exists('gzread')) {
            // Read signature data (first 3 bytes)
            $fh = fopen($pFilename, 'rb');
            $data = fread($fh, 2);
            fclose($fh);
        }

        return $data == chr(0x1F) . chr(0x8B);
    }

    private static function matchXml(string $name, string $field): bool
    {
        return 1 === preg_match("/^(gnm|gmr):$field$/", $name);
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

        $xml = new XMLReader();
        $xml->xml($this->securityScanner->scanFile('compress.zlib://' . realpath($pFilename)), null, Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);

        $worksheetNames = [];
        while ($xml->read()) {
            if (self::matchXml($xml->name, 'SheetName') && $xml->nodeType == XMLReader::ELEMENT) {
                $xml->read(); //    Move onto the value node
                $worksheetNames[] = (string) $xml->value;
            } elseif (self::matchXml($xml->name, 'Sheets')) {
                //    break out of the loop once we've got our sheet names rather than parse the entire file
                break;
            }
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

        $xml = new XMLReader();
        $xml->xml($this->securityScanner->scanFile('compress.zlib://' . realpath($pFilename)), null, Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);

        $worksheetInfo = [];
        while ($xml->read()) {
            if (self::matchXml($xml->name, 'Sheet') && $xml->nodeType == XMLReader::ELEMENT) {
                $tmpInfo = [
                    'worksheetName' => '',
                    'lastColumnLetter' => 'A',
                    'lastColumnIndex' => 0,
                    'totalRows' => 0,
                    'totalColumns' => 0,
                ];

                while ($xml->read()) {
                    if ($xml->nodeType == XMLReader::ELEMENT) {
                        if (self::matchXml($xml->name, 'Name')) {
                            $xml->read(); //    Move onto the value node
                            $tmpInfo['worksheetName'] = (string) $xml->value;
                        } elseif (self::matchXml($xml->name, 'MaxCol')) {
                            $xml->read(); //    Move onto the value node
                            $tmpInfo['lastColumnIndex'] = (int) $xml->value;
                            $tmpInfo['totalColumns'] = (int) $xml->value + 1;
                        } elseif (self::matchXml($xml->name, 'MaxRow')) {
                            $xml->read(); //    Move onto the value node
                            $tmpInfo['totalRows'] = (int) $xml->value + 1;

                            break;
                        }
                    }
                }
                $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
                $worksheetInfo[] = $tmpInfo;
            }
        }

        return $worksheetInfo;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function gzfileGetContents($filename)
    {
        $file = @gzopen($filename, 'rb');
        $data = '';
        if ($file !== false) {
            while (!gzeof($file)) {
                $data .= gzread($file, 1024);
            }
            gzclose($file);
        }

        return $data;
    }

    private static $mappings = [
        'borderStyle' => [
            '0' => Border::BORDER_NONE,
            '1' => Border::BORDER_THIN,
            '2' => Border::BORDER_MEDIUM,
            '3' => Border::BORDER_SLANTDASHDOT,
            '4' => Border::BORDER_DASHED,
            '5' => Border::BORDER_THICK,
            '6' => Border::BORDER_DOUBLE,
            '7' => Border::BORDER_DOTTED,
            '8' => Border::BORDER_MEDIUMDASHED,
            '9' => Border::BORDER_DASHDOT,
            '10' => Border::BORDER_MEDIUMDASHDOT,
            '11' => Border::BORDER_DASHDOTDOT,
            '12' => Border::BORDER_MEDIUMDASHDOTDOT,
            '13' => Border::BORDER_MEDIUMDASHDOTDOT,
        ],
        'dataType' => [
            '10' => DataType::TYPE_NULL,
            '20' => DataType::TYPE_BOOL,
            '30' => DataType::TYPE_NUMERIC, // Integer doesn't exist in Excel
            '40' => DataType::TYPE_NUMERIC, // Float
            '50' => DataType::TYPE_ERROR,
            '60' => DataType::TYPE_STRING,
            //'70':        //    Cell Range
            //'80':        //    Array
        ],
        'fillType' => [
            '1' => Fill::FILL_SOLID,
            '2' => Fill::FILL_PATTERN_DARKGRAY,
            '3' => Fill::FILL_PATTERN_MEDIUMGRAY,
            '4' => Fill::FILL_PATTERN_LIGHTGRAY,
            '5' => Fill::FILL_PATTERN_GRAY125,
            '6' => Fill::FILL_PATTERN_GRAY0625,
            '7' => Fill::FILL_PATTERN_DARKHORIZONTAL, // horizontal stripe
            '8' => Fill::FILL_PATTERN_DARKVERTICAL, // vertical stripe
            '9' => Fill::FILL_PATTERN_DARKDOWN, // diagonal stripe
            '10' => Fill::FILL_PATTERN_DARKUP, // reverse diagonal stripe
            '11' => Fill::FILL_PATTERN_DARKGRID, // diagoanl crosshatch
            '12' => Fill::FILL_PATTERN_DARKTRELLIS, // thick diagonal crosshatch
            '13' => Fill::FILL_PATTERN_LIGHTHORIZONTAL,
            '14' => Fill::FILL_PATTERN_LIGHTVERTICAL,
            '15' => Fill::FILL_PATTERN_LIGHTUP,
            '16' => Fill::FILL_PATTERN_LIGHTDOWN,
            '17' => Fill::FILL_PATTERN_LIGHTGRID, // thin horizontal crosshatch
            '18' => Fill::FILL_PATTERN_LIGHTTRELLIS, // thin diagonal crosshatch
        ],
        'horizontal' => [
            '1' => Alignment::HORIZONTAL_GENERAL,
            '2' => Alignment::HORIZONTAL_LEFT,
            '4' => Alignment::HORIZONTAL_RIGHT,
            '8' => Alignment::HORIZONTAL_CENTER,
            '16' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            '32' => Alignment::HORIZONTAL_JUSTIFY,
            '64' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
        ],
        'underline' => [
            '1' => Font::UNDERLINE_SINGLE,
            '2' => Font::UNDERLINE_DOUBLE,
            '3' => Font::UNDERLINE_SINGLEACCOUNTING,
            '4' => Font::UNDERLINE_DOUBLEACCOUNTING,
        ],
        'vertical' => [
            '1' => Alignment::VERTICAL_TOP,
            '2' => Alignment::VERTICAL_BOTTOM,
            '4' => Alignment::VERTICAL_CENTER,
            '8' => Alignment::VERTICAL_JUSTIFY,
        ],
    ];

    public static function gnumericMappings(): array
    {
        return self::$mappings;
    }

    private function docPropertiesOld(SimpleXMLElement $gnmXML): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($gnmXML->Summary->Item as $summaryItem) {
            $propertyName = $summaryItem->name;
            $propertyValue = $summaryItem->{'val-string'};
            switch ($propertyName) {
                case 'title':
                    $docProps->setTitle(trim($propertyValue));

                    break;
                case 'comments':
                    $docProps->setDescription(trim($propertyValue));

                    break;
                case 'keywords':
                    $docProps->setKeywords(trim($propertyValue));

                    break;
                case 'category':
                    $docProps->setCategory(trim($propertyValue));

                    break;
                case 'manager':
                    $docProps->setManager(trim($propertyValue));

                    break;
                case 'author':
                    $docProps->setCreator(trim($propertyValue));
                    $docProps->setLastModifiedBy(trim($propertyValue));

                    break;
                case 'company':
                    $docProps->setCompany(trim($propertyValue));

                    break;
            }
        }
    }

    private function docPropertiesDC(SimpleXMLElement $officePropertyDC): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($officePropertyDC as $propertyName => $propertyValue) {
            $propertyValue = trim((string) $propertyValue);
            switch ($propertyName) {
                case 'title':
                    $docProps->setTitle($propertyValue);

                    break;
                case 'subject':
                    $docProps->setSubject($propertyValue);

                    break;
                case 'creator':
                    $docProps->setCreator($propertyValue);
                    $docProps->setLastModifiedBy($propertyValue);

                    break;
                case 'date':
                    $creationDate = strtotime($propertyValue);
                    $docProps->setCreated($creationDate);
                    $docProps->setModified($creationDate);

                    break;
                case 'description':
                    $docProps->setDescription($propertyValue);

                    break;
            }
        }
    }

    private function docPropertiesMeta(SimpleXMLElement $officePropertyMeta, array $namespacesMeta): void
    {
        $docProps = $this->spreadsheet->getProperties();
        foreach ($officePropertyMeta as $propertyName => $propertyValue) {
            $attributes = $propertyValue->attributes($namespacesMeta['meta']);
            $propertyValue = trim((string) $propertyValue);
            switch ($propertyName) {
                case 'keyword':
                    $docProps->setKeywords($propertyValue);

                    break;
                case 'initial-creator':
                    $docProps->setCreator($propertyValue);
                    $docProps->setLastModifiedBy($propertyValue);

                    break;
                case 'creation-date':
                    $creationDate = strtotime($propertyValue);
                    $docProps->setCreated($creationDate);
                    $docProps->setModified($creationDate);

                    break;
                case 'user-defined':
                    [, $attrName] = explode(':', $attributes['name']);
                    switch ($attrName) {
                        case 'publisher':
                            $docProps->setCompany($propertyValue);

                            break;
                        case 'category':
                            $docProps->setCategory($propertyValue);

                            break;
                        case 'manager':
                            $docProps->setManager($propertyValue);

                            break;
                    }

                    break;
            }
        }
    }

    private function docProperties(SimpleXMLElement $xml, SimpleXMLElement $gnmXML, array $namespacesMeta): void
    {
        if (isset($namespacesMeta['office'])) {
            $officeXML = $xml->children($namespacesMeta['office']);
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;

            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = [];
                if (isset($namespacesMeta['dc'])) {
                    $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
                }
                $this->docPropertiesDC($officePropertyDC);

                $officePropertyMeta = [];
                if (isset($namespacesMeta['meta'])) {
                    $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
                }
                $this->docPropertiesMeta($officePropertyMeta, $namespacesMeta);
            }
        } elseif (isset($gnmXML->Summary)) {
            $this->docPropertiesOld($gnmXML);
        }
    }

    private function processComments(SimpleXMLElement $sheet): void
    {
        if ((!$this->readDataOnly) && (isset($sheet->Objects))) {
            foreach ($sheet->Objects->children($this->gnm, true) as $key => $comment) {
                $commentAttributes = $comment->attributes();
                //    Only comment objects are handled at the moment
                if ($commentAttributes->Text) {
                    $this->spreadsheet->getActiveSheet()->getComment((string) $commentAttributes->ObjectBound)->setAuthor((string) $commentAttributes->Author)->setText($this->parseRichText((string) $commentAttributes->Text));
                }
            }
        }
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

    /**
     * Loads from file into Spreadsheet instance.
     */
    public function loadIntoExisting(string $pFilename, Spreadsheet $spreadsheet): Spreadsheet
    {
        $this->spreadsheet = $spreadsheet;
        File::assertFile($pFilename);

        $gFileData = $this->gzfileGetContents($pFilename);

        $xml2 = simplexml_load_string($this->securityScanner->scan($gFileData), 'SimpleXMLElement', Settings::getLibXmlLoaderOptions());
        $xml = ($xml2 !== false) ? $xml2 : new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
        $namespacesMeta = $xml->getNamespaces(true);
        $this->gnm = array_key_exists('gmr', $namespacesMeta) ? 'gmr' : 'gnm';

        $gnmXML = $xml->children($namespacesMeta[$this->gnm]);
        $this->docProperties($xml, $gnmXML, $namespacesMeta);

        $worksheetID = 0;
        foreach ($gnmXML->Sheets->Sheet as $sheet) {
            $worksheetName = (string) $sheet->Name;
            if ((isset($this->loadSheetsOnly)) && (!in_array($worksheetName, $this->loadSheetsOnly))) {
                continue;
            }

            $maxRow = $maxCol = 0;

            // Create new Worksheet
            $this->spreadsheet->createSheet();
            $this->spreadsheet->setActiveSheetIndex($worksheetID);
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $this->spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);

            if (!$this->readDataOnly) {
                (new PageSetup($this->spreadsheet, $this->gnm))
                    ->printInformation($sheet)
                    ->sheetMargins($sheet);
            }

            foreach ($sheet->Cells->Cell as $cell) {
                $cellAttributes = $cell->attributes();
                $row = (int) $cellAttributes->Row + 1;
                $column = (int) $cellAttributes->Col;

                if ($row > $maxRow) {
                    $maxRow = $row;
                }
                if ($column > $maxCol) {
                    $maxCol = $column;
                }

                $column = Coordinate::stringFromColumnIndex($column + 1);

                // Read cell?
                if ($this->getReadFilter() !== null) {
                    if (!$this->getReadFilter()->readCell($column, $row, $worksheetName)) {
                        continue;
                    }
                }

                $ValueType = $cellAttributes->ValueType;
                $ExprID = (string) $cellAttributes->ExprID;
                $type = DataType::TYPE_FORMULA;
                if ($ExprID > '') {
                    if (((string) $cell) > '') {
                        $this->expressions[$ExprID] = [
                            'column' => $cellAttributes->Col,
                            'row' => $cellAttributes->Row,
                            'formula' => (string) $cell,
                        ];
                    } else {
                        $expression = $this->expressions[$ExprID];

                        $cell = $this->referenceHelper->updateFormulaReferences(
                            $expression['formula'],
                            'A1',
                            $cellAttributes->Col - $expression['column'],
                            $cellAttributes->Row - $expression['row'],
                            $worksheetName
                        );
                    }
                    $type = DataType::TYPE_FORMULA;
                } else {
                    $vtype = (string) $ValueType;
                    if (array_key_exists($vtype, self::$mappings['dataType'])) {
                        $type = self::$mappings['dataType'][$vtype];
                    }
                    if ($vtype == '20') {        //    Boolean
                        $cell = $cell == 'TRUE';
                    }
                }
                $this->spreadsheet->getActiveSheet()->getCell($column . $row)->setValueExplicit((string) $cell, $type);
            }

            $this->processComments($sheet);

            foreach ($sheet->Styles->StyleRegion as $styleRegion) {
                $styleAttributes = $styleRegion->attributes();
                if (
                    ($styleAttributes['startRow'] <= $maxRow) &&
                    ($styleAttributes['startCol'] <= $maxCol)
                ) {
                    $startColumn = Coordinate::stringFromColumnIndex((int) $styleAttributes['startCol'] + 1);
                    $startRow = $styleAttributes['startRow'] + 1;

                    $endColumn = ($styleAttributes['endCol'] > $maxCol) ? $maxCol : (int) $styleAttributes['endCol'];
                    $endColumn = Coordinate::stringFromColumnIndex($endColumn + 1);
                    $endRow = 1 + (($styleAttributes['endRow'] > $maxRow) ? $maxRow : $styleAttributes['endRow']);
                    $cellRange = $startColumn . $startRow . ':' . $endColumn . $endRow;

                    $styleAttributes = $styleRegion->Style->attributes();

                    $styleArray = [];
                    //    We still set the number format mask for date/time values, even if readDataOnly is true
                    $formatCode = (string) $styleAttributes['Format'];
                    if (Date::isDateTimeFormatCode($formatCode)) {
                        $styleArray['numberFormat']['formatCode'] = $formatCode;
                    }
                    if (!$this->readDataOnly) {
                        //    If readDataOnly is false, we set all formatting information
                        $styleArray['numberFormat']['formatCode'] = $formatCode;

                        self::addStyle2($styleArray, 'alignment', 'horizontal', $styleAttributes['HAlign']);
                        self::addStyle2($styleArray, 'alignment', 'vertical', $styleAttributes['VAlign']);
                        $styleArray['alignment']['wrapText'] = $styleAttributes['WrapText'] == '1';
                        $styleArray['alignment']['textRotation'] = $this->calcRotation($styleAttributes);
                        $styleArray['alignment']['shrinkToFit'] = $styleAttributes['ShrinkToFit'] == '1';
                        $styleArray['alignment']['indent'] = ((int) ($styleAttributes['Indent']) > 0) ? $styleAttributes['indent'] : 0;

                        $this->addColors($styleArray, $styleAttributes);

                        $fontAttributes = $styleRegion->Style->Font->attributes();
                        $styleArray['font']['name'] = (string) $styleRegion->Style->Font;
                        $styleArray['font']['size'] = (int) ($fontAttributes['Unit']);
                        $styleArray['font']['bold'] = $fontAttributes['Bold'] == '1';
                        $styleArray['font']['italic'] = $fontAttributes['Italic'] == '1';
                        $styleArray['font']['strikethrough'] = $fontAttributes['StrikeThrough'] == '1';
                        self::addStyle2($styleArray, 'font', 'underline', $fontAttributes['Underline']);

                        switch ($fontAttributes['Script']) {
                            case '1':
                                $styleArray['font']['superscript'] = true;

                                break;
                            case '-1':
                                $styleArray['font']['subscript'] = true;

                                break;
                        }

                        if (isset($styleRegion->Style->StyleBorder)) {
                            $srssb = $styleRegion->Style->StyleBorder;
                            $this->addBorderStyle($srssb, $styleArray, 'top');
                            $this->addBorderStyle($srssb, $styleArray, 'bottom');
                            $this->addBorderStyle($srssb, $styleArray, 'left');
                            $this->addBorderStyle($srssb, $styleArray, 'right');
                            $this->addBorderDiagonal($srssb, $styleArray);
                        }
                        if (isset($styleRegion->Style->HyperLink)) {
                            //    TO DO
                            $hyperlink = $styleRegion->Style->HyperLink->attributes();
                        }
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle($cellRange)->applyFromArray($styleArray);
                }
            }

            $this->processColumnWidths($sheet, $maxCol);
            $this->processRowHeights($sheet, $maxRow);
            $this->processMergedCells($sheet);

            ++$worksheetID;
        }

        $this->processDefinedNames($gnmXML);

        // Return
        return $this->spreadsheet;
    }

    private function addBorderDiagonal(SimpleXMLElement $srssb, array &$styleArray): void
    {
        if (isset($srssb->Diagonal, $srssb->{'Rev-Diagonal'})) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->Diagonal->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_BOTH;
        } elseif (isset($srssb->Diagonal)) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->Diagonal->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_UP;
        } elseif (isset($srssb->{'Rev-Diagonal'})) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->{'Rev-Diagonal'}->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_DOWN;
        }
    }

    private function addBorderStyle(SimpleXMLElement $srssb, array &$styleArray, string $direction): void
    {
        $ucDirection = ucfirst($direction);
        if (isset($srssb->$ucDirection)) {
            $styleArray['borders'][$direction] = self::parseBorderAttributes($srssb->$ucDirection->attributes());
        }
    }

    private function processMergedCells(SimpleXMLElement $sheet): void
    {
        //    Handle Merged Cells in this worksheet
        if (isset($sheet->MergedRegions)) {
            foreach ($sheet->MergedRegions->Merge as $mergeCells) {
                if (strpos($mergeCells, ':') !== false) {
                    $this->spreadsheet->getActiveSheet()->mergeCells($mergeCells);
                }
            }
        }
    }

    private function processColumnLoop(int $c, int $maxCol, SimpleXMLElement $columnOverride, float $defaultWidth): int
    {
        $columnAttributes = $columnOverride->attributes();
        $column = $columnAttributes['No'];
        $columnWidth = ((float) $columnAttributes['Unit']) / 5.4;
        $hidden = (isset($columnAttributes['Hidden'])) && ((string) $columnAttributes['Hidden'] == '1');
        $columnCount = (isset($columnAttributes['Count'])) ? $columnAttributes['Count'] : 1;
        while ($c < $column) {
            $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($defaultWidth);
            ++$c;
        }
        while (($c < ($column + $columnCount)) && ($c <= $maxCol)) {
            $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($columnWidth);
            if ($hidden) {
                $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setVisible(false);
            }
            ++$c;
        }

        return $c;
    }

    private function processColumnWidths(SimpleXMLElement $sheet, int $maxCol): void
    {
        if ((!$this->readDataOnly) && (isset($sheet->Cols))) {
            //    Column Widths
            $columnAttributes = $sheet->Cols->attributes();
            $defaultWidth = $columnAttributes['DefaultSizePts'] / 5.4;
            $c = 0;
            foreach ($sheet->Cols->ColInfo as $columnOverride) {
                $c = $this->processColumnLoop($c, $maxCol, $columnOverride, $defaultWidth);
            }
            while ($c <= $maxCol) {
                $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($defaultWidth);
                ++$c;
            }
        }
    }

    private function processRowLoop(int $r, int $maxRow, SimpleXMLElement $rowOverride, float $defaultHeight): int
    {
        $rowAttributes = $rowOverride->attributes();
        $row = $rowAttributes['No'];
        $rowHeight = (float) $rowAttributes['Unit'];
        $hidden = (isset($rowAttributes['Hidden'])) && ((string) $rowAttributes['Hidden'] == '1');
        $rowCount = (isset($rowAttributes['Count'])) ? $rowAttributes['Count'] : 1;
        while ($r < $row) {
            ++$r;
            $this->spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
        }
        while (($r < ($row + $rowCount)) && ($r < $maxRow)) {
            ++$r;
            $this->spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($rowHeight);
            if ($hidden) {
                $this->spreadsheet->getActiveSheet()->getRowDimension($r)->setVisible(false);
            }
        }

        return $r;
    }

    private function processRowHeights(SimpleXMLElement $sheet, int $maxRow): void
    {
        if ((!$this->readDataOnly) && (isset($sheet->Rows))) {
            //    Row Heights
            $rowAttributes = $sheet->Rows->attributes();
            $defaultHeight = (float) $rowAttributes['DefaultSizePts'];
            $r = 0;

            foreach ($sheet->Rows->RowInfo as $rowOverride) {
                $r = $this->processRowLoop($r, $maxRow, $rowOverride, $defaultHeight);
            }
            // never executed, I can't figure out any circumstances
            // under which it would be executed, and, even if
            // such exist, I'm not convinced this is needed.
            //while ($r < $maxRow) {
            //    ++$r;
            //    $this->spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
            //}
        }
    }

    private function processDefinedNames(SimpleXMLElement $gnmXML): void
    {
        //    Loop through definedNames (global named ranges)
        if (isset($gnmXML->Names)) {
            foreach ($gnmXML->Names->Name as $definedName) {
                $name = (string) $definedName->name;
                $value = (string) $definedName->value;
                if (stripos($value, '#REF!') !== false) {
                    continue;
                }

                [$worksheetName] = Worksheet::extractSheetTitle($value, true);
                $worksheetName = trim($worksheetName, "'");
                $worksheet = $this->spreadsheet->getSheetByName($worksheetName);
                // Worksheet might still be null if we're only loading selected sheets rather than the full spreadsheet
                if ($worksheet !== null) {
                    $this->spreadsheet->addDefinedName(DefinedName::createInstance($name, $worksheet, $value));
                }
            }
        }
    }

    private function calcRotation(SimpleXMLElement $styleAttributes): int
    {
        $rotation = (int) $styleAttributes->Rotation;
        if ($rotation >= 270 && $rotation <= 360) {
            $rotation -= 360;
        }
        $rotation = (abs($rotation) > 90) ? 0 : $rotation;

        return $rotation;
    }

    private static function addStyle(array &$styleArray, string $key, string $value): void
    {
        if (array_key_exists($value, self::$mappings[$key])) {
            $styleArray[$key] = self::$mappings[$key][$value];
        }
    }

    private static function addStyle2(array &$styleArray, string $key1, string $key, string $value): void
    {
        if (array_key_exists($value, self::$mappings[$key])) {
            $styleArray[$key1][$key] = self::$mappings[$key][$value];
        }
    }

    private static function parseBorderAttributes($borderAttributes)
    {
        $styleArray = [];
        if (isset($borderAttributes['Color'])) {
            $styleArray['color']['rgb'] = self::parseGnumericColour($borderAttributes['Color']);
        }

        self::addStyle($styleArray, 'borderStyle', $borderAttributes['Style']);

        return $styleArray;
    }

    private function parseRichText($is)
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }

    private static function parseGnumericColour($gnmColour)
    {
        [$gnmR, $gnmG, $gnmB] = explode(':', $gnmColour);
        $gnmR = substr(str_pad($gnmR, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmG = substr(str_pad($gnmG, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmB = substr(str_pad($gnmB, 4, '0', STR_PAD_RIGHT), 0, 2);

        return $gnmR . $gnmG . $gnmB;
    }

    private function addColors(array &$styleArray, SimpleXMLElement $styleAttributes): void
    {
        $RGB = self::parseGnumericColour($styleAttributes['Fore']);
        $styleArray['font']['color']['rgb'] = $RGB;
        $RGB = self::parseGnumericColour($styleAttributes['Back']);
        $shade = (string) $styleAttributes['Shade'];
        if (($RGB != '000000') || ($shade != '0')) {
            $RGB2 = self::parseGnumericColour($styleAttributes['PatternColor']);
            if ($shade == '1') {
                $styleArray['fill']['startColor']['rgb'] = $RGB;
                $styleArray['fill']['endColor']['rgb'] = $RGB2;
            } else {
                $styleArray['fill']['endColor']['rgb'] = $RGB;
                $styleArray['fill']['startColor']['rgb'] = $RGB2;
            }
            self::addStyle2($styleArray, 'fill', 'fillType', $shade);
        }
    }
}
