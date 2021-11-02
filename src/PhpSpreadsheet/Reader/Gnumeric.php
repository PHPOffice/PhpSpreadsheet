<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric\PageSetup;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric\Styles;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use XMLReader;

class Gnumeric extends BaseReader
{
    const NAMESPACE_GNM = 'http://www.gnumeric.org/v10.dtd'; // gmr in old sheets

    const NAMESPACE_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    const NAMESPACE_OFFICE = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';

    const NAMESPACE_XLINK = 'http://www.w3.org/1999/xlink';

    const NAMESPACE_DC = 'http://purl.org/dc/elements/1.1/';

    const NAMESPACE_META = 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0';

    const NAMESPACE_OOO = 'http://openoffice.org/2004/office';

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

    /** @var ReferenceHelper */
    private $referenceHelper;

    /** @var array */
    public static $mappings = [
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
    ];

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
     */
    public function canRead(string $filename): bool
    {
        // Check if gzlib functions are available
        if (File::testFileNoThrow($filename) && function_exists('gzread')) {
            // Read signature data (first 3 bytes)
            $fh = fopen($filename, 'rb');
            if ($fh !== false) {
                $data = fread($fh, 2);
                fclose($fh);
            }
        }

        return isset($data) && $data === chr(0x1F) . chr(0x8B);
    }

    private static function matchXml(XMLReader $xml, string $expectedLocalName): bool
    {
        return $xml->namespaceURI === self::NAMESPACE_GNM
            && $xml->localName === $expectedLocalName
            && $xml->nodeType === XMLReader::ELEMENT;
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
            if (self::matchXml($xml, 'SheetName')) {
                $xml->read(); //    Move onto the value node
                $worksheetNames[] = (string) $xml->value;
            } elseif (self::matchXml($xml, 'Sheets')) {
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
            if (self::matchXml($xml, 'Sheet')) {
                $tmpInfo = [
                    'worksheetName' => '',
                    'lastColumnLetter' => 'A',
                    'lastColumnIndex' => 0,
                    'totalRows' => 0,
                    'totalColumns' => 0,
                ];

                while ($xml->read()) {
                    if (self::matchXml($xml, 'Name')) {
                        $xml->read(); //    Move onto the value node
                        $tmpInfo['worksheetName'] = (string) $xml->value;
                    } elseif (self::matchXml($xml, 'MaxCol')) {
                        $xml->read(); //    Move onto the value node
                        $tmpInfo['lastColumnIndex'] = (int) $xml->value;
                        $tmpInfo['totalColumns'] = (int) $xml->value + 1;
                    } elseif (self::matchXml($xml, 'MaxRow')) {
                        $xml->read(); //    Move onto the value node
                        $tmpInfo['totalRows'] = (int) $xml->value + 1;

                        break;
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

    public static function gnumericMappings(): array
    {
        return array_merge(self::$mappings, Styles::$mappings);
    }

    private function processComments(SimpleXMLElement $sheet): void
    {
        if ((!$this->readDataOnly) && (isset($sheet->Objects))) {
            foreach ($sheet->Objects->children(self::NAMESPACE_GNM) as $key => $comment) {
                $commentAttributes = $comment->attributes();
                //    Only comment objects are handled at the moment
                if ($commentAttributes && $commentAttributes->Text) {
                    $this->spreadsheet->getActiveSheet()->getComment((string) $commentAttributes->ObjectBound)
                        ->setAuthor((string) $commentAttributes->Author)
                        ->setText($this->parseRichText((string) $commentAttributes->Text));
                }
            }
        }
    }

    /**
     * @param mixed $value
     */
    private static function testSimpleXml($value): SimpleXMLElement
    {
        return ($value instanceof SimpleXMLElement) ? $value : new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @return Spreadsheet
     */
    public function load(string $filename, int $flags = 0)
    {
        $this->processFlags($flags);

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
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
        $xml = self::testSimpleXml($xml2);

        $gnmXML = $xml->children(self::NAMESPACE_GNM);
        (new Properties($this->spreadsheet))->readProperties($xml, $gnmXML);

        $worksheetID = 0;
        foreach ($gnmXML->Sheets->Sheet as $sheetOrNull) {
            $sheet = self::testSimpleXml($sheetOrNull);
            $worksheetName = (string) $sheet->Name;
            if (is_array($this->loadSheetsOnly) && !in_array($worksheetName, $this->loadSheetsOnly, true)) {
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
                (new PageSetup($this->spreadsheet))
                    ->printInformation($sheet)
                    ->sheetMargins($sheet);
            }

            foreach ($sheet->Cells->Cell as $cellOrNull) {
                $cell = self::testSimpleXml($cellOrNull);
                $cellAttributes = self::testSimpleXml($cell->attributes());
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
                    if ($vtype === '20') {        //    Boolean
                        $cell = $cell == 'TRUE';
                    }
                }
                $this->spreadsheet->getActiveSheet()->getCell($column . $row)->setValueExplicit((string) $cell, $type);
            }

            if ($sheet->Styles !== null) {
                (new Styles($this->spreadsheet, $this->readDataOnly))->read($sheet, $maxRow, $maxCol);
            }

            $this->processComments($sheet);
            $this->processColumnWidths($sheet, $maxCol);
            $this->processRowHeights($sheet, $maxRow);
            $this->processMergedCells($sheet);
            $this->processAutofilter($sheet);

            ++$worksheetID;
        }

        $this->processDefinedNames($gnmXML);

        // Return
        return $this->spreadsheet;
    }

    private function processMergedCells(?SimpleXMLElement $sheet): void
    {
        //    Handle Merged Cells in this worksheet
        if ($sheet !== null && isset($sheet->MergedRegions)) {
            foreach ($sheet->MergedRegions->Merge as $mergeCells) {
                if (strpos((string) $mergeCells, ':') !== false) {
                    $this->spreadsheet->getActiveSheet()->mergeCells($mergeCells);
                }
            }
        }
    }

    private function processAutofilter(?SimpleXMLElement $sheet): void
    {
        if ($sheet !== null && isset($sheet->Filters)) {
            foreach ($sheet->Filters->Filter as $autofilter) {
                if ($autofilter !== null) {
                    $attributes = $autofilter->attributes();
                    if (isset($attributes['Area'])) {
                        $this->spreadsheet->getActiveSheet()->setAutoFilter((string) $attributes['Area']);
                    }
                }
            }
        }
    }

    private function setColumnWidth(int $whichColumn, float $defaultWidth): void
    {
        $columnDimension = $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($whichColumn + 1));
        if ($columnDimension !== null) {
            $columnDimension->setWidth($defaultWidth);
        }
    }

    private function setColumnInvisible(int $whichColumn): void
    {
        $columnDimension = $this->spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($whichColumn + 1));
        if ($columnDimension !== null) {
            $columnDimension->setVisible(false);
        }
    }

    private function processColumnLoop(int $whichColumn, int $maxCol, ?SimpleXMLElement $columnOverride, float $defaultWidth): int
    {
        $columnOverride = self::testSimpleXml($columnOverride);
        $columnAttributes = self::testSimpleXml($columnOverride->attributes());
        $column = $columnAttributes['No'];
        $columnWidth = ((float) $columnAttributes['Unit']) / 5.4;
        $hidden = (isset($columnAttributes['Hidden'])) && ((string) $columnAttributes['Hidden'] == '1');
        $columnCount = (int) ($columnAttributes['Count'] ?? 1);
        while ($whichColumn < $column) {
            $this->setColumnWidth($whichColumn, $defaultWidth);
            ++$whichColumn;
        }
        while (($whichColumn < ($column + $columnCount)) && ($whichColumn <= $maxCol)) {
            $this->setColumnWidth($whichColumn, $columnWidth);
            if ($hidden) {
                $this->setColumnInvisible($whichColumn);
            }
            ++$whichColumn;
        }

        return $whichColumn;
    }

    private function processColumnWidths(?SimpleXMLElement $sheet, int $maxCol): void
    {
        if ((!$this->readDataOnly) && $sheet !== null && (isset($sheet->Cols))) {
            //    Column Widths
            $defaultWidth = 0;
            $columnAttributes = $sheet->Cols->attributes();
            if ($columnAttributes !== null) {
                $defaultWidth = $columnAttributes['DefaultSizePts'] / 5.4;
            }
            $whichColumn = 0;
            foreach ($sheet->Cols->ColInfo as $columnOverride) {
                $whichColumn = $this->processColumnLoop($whichColumn, $maxCol, $columnOverride, $defaultWidth);
            }
            while ($whichColumn <= $maxCol) {
                $this->setColumnWidth($whichColumn, $defaultWidth);
                ++$whichColumn;
            }
        }
    }

    private function setRowHeight(int $whichRow, float $defaultHeight): void
    {
        $rowDimension = $this->spreadsheet->getActiveSheet()->getRowDimension($whichRow);
        if ($rowDimension !== null) {
            $rowDimension->setRowHeight($defaultHeight);
        }
    }

    private function setRowInvisible(int $whichRow): void
    {
        $rowDimension = $this->spreadsheet->getActiveSheet()->getRowDimension($whichRow);
        if ($rowDimension !== null) {
            $rowDimension->setVisible(false);
        }
    }

    private function processRowLoop(int $whichRow, int $maxRow, ?SimpleXMLElement $rowOverride, float $defaultHeight): int
    {
        $rowOverride = self::testSimpleXml($rowOverride);
        $rowAttributes = self::testSimpleXml($rowOverride->attributes());
        $row = $rowAttributes['No'];
        $rowHeight = (float) $rowAttributes['Unit'];
        $hidden = (isset($rowAttributes['Hidden'])) && ((string) $rowAttributes['Hidden'] == '1');
        $rowCount = (int) ($rowAttributes['Count'] ?? 1);
        while ($whichRow < $row) {
            ++$whichRow;
            $this->setRowHeight($whichRow, $defaultHeight);
        }
        while (($whichRow < ($row + $rowCount)) && ($whichRow < $maxRow)) {
            ++$whichRow;
            $this->setRowHeight($whichRow, $rowHeight);
            if ($hidden) {
                $this->setRowInvisible($whichRow);
            }
        }

        return $whichRow;
    }

    private function processRowHeights(?SimpleXMLElement $sheet, int $maxRow): void
    {
        if ((!$this->readDataOnly) && $sheet !== null && (isset($sheet->Rows))) {
            //    Row Heights
            $defaultHeight = 0;
            $rowAttributes = $sheet->Rows->attributes();
            if ($rowAttributes !== null) {
                $defaultHeight = (float) $rowAttributes['DefaultSizePts'];
            }
            $whichRow = 0;

            foreach ($sheet->Rows->RowInfo as $rowOverride) {
                $whichRow = $this->processRowLoop($whichRow, $maxRow, $rowOverride, $defaultHeight);
            }
            // never executed, I can't figure out any circumstances
            // under which it would be executed, and, even if
            // such exist, I'm not convinced this is needed.
            //while ($whichRow < $maxRow) {
            //    ++$whichRow;
            //    $this->spreadsheet->getActiveSheet()->getRowDimension($whichRow)->setRowHeight($defaultHeight);
            //}
        }
    }

    private function processDefinedNames(?SimpleXMLElement $gnmXML): void
    {
        //    Loop through definedNames (global named ranges)
        if ($gnmXML !== null && isset($gnmXML->Names)) {
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

    private function parseRichText(string $is): RichText
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }
}
