<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\NamedRange;
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
use XMLReader;

class Gnumeric extends BaseReader
{
    /**
     * Shared Expressions.
     *
     * @var array
     */
    private $expressions = [];

    private $referenceHelper;

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
        if (!function_exists('gzread')) {
            throw new Exception('gzlib library is not enabled');
        }

        // Read signature data (first 3 bytes)
        $fh = fopen($pFilename, 'rb');
        $data = fread($fh, 2);
        fclose($fh);

        return $data == chr(0x1F) . chr(0x8B);
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
            if ($xml->name == 'gnm:SheetName' && $xml->nodeType == XMLReader::ELEMENT) {
                $xml->read(); //    Move onto the value node
                $worksheetNames[] = (string) $xml->value;
            } elseif ($xml->name == 'gnm:Sheets') {
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
            if ($xml->name == 'gnm:Sheet' && $xml->nodeType == XMLReader::ELEMENT) {
                $tmpInfo = [
                    'worksheetName' => '',
                    'lastColumnLetter' => 'A',
                    'lastColumnIndex' => 0,
                    'totalRows' => 0,
                    'totalColumns' => 0,
                ];

                while ($xml->read()) {
                    if ($xml->name == 'gnm:Name' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read(); //    Move onto the value node
                        $tmpInfo['worksheetName'] = (string) $xml->value;
                    } elseif ($xml->name == 'gnm:MaxCol' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read(); //    Move onto the value node
                        $tmpInfo['lastColumnIndex'] = (int) $xml->value;
                        $tmpInfo['totalColumns'] = (int) $xml->value + 1;
                    } elseif ($xml->name == 'gnm:MaxRow' && $xml->nodeType == XMLReader::ELEMENT) {
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

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
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

        $gFileData = $this->gzfileGetContents($pFilename);

        $xml = simplexml_load_string($this->securityScanner->scan($gFileData), 'SimpleXMLElement', Settings::getLibXmlLoaderOptions());
        $namespacesMeta = $xml->getNamespaces(true);

        $gnmXML = $xml->children($namespacesMeta['gnm']);

        $docProps = $spreadsheet->getProperties();
        //    Document Properties are held differently, depending on the version of Gnumeric
        if (isset($namespacesMeta['office'])) {
            $officeXML = $xml->children($namespacesMeta['office']);
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;

            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = [];
                if (isset($namespacesMeta['dc'])) {
                    $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
                }
                foreach ($officePropertyDC as $propertyName => $propertyValue) {
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'title':
                            $docProps->setTitle(trim($propertyValue));

                            break;
                        case 'subject':
                            $docProps->setSubject(trim($propertyValue));

                            break;
                        case 'creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));

                            break;
                        case 'date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);

                            break;
                        case 'description':
                            $docProps->setDescription(trim($propertyValue));

                            break;
                    }
                }
                $officePropertyMeta = [];
                if (isset($namespacesMeta['meta'])) {
                    $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
                }
                foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                    $attributes = $propertyValue->attributes($namespacesMeta['meta']);
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'keyword':
                            $docProps->setKeywords(trim($propertyValue));

                            break;
                        case 'initial-creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));

                            break;
                        case 'creation-date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);

                            break;
                        case 'user-defined':
                            [, $attrName] = explode(':', $attributes['name']);
                            switch ($attrName) {
                                case 'publisher':
                                    $docProps->setCompany(trim($propertyValue));

                                    break;
                                case 'category':
                                    $docProps->setCategory(trim($propertyValue));

                                    break;
                                case 'manager':
                                    $docProps->setManager(trim($propertyValue));

                                    break;
                            }

                            break;
                    }
                }
            }
        } elseif (isset($gnmXML->Summary)) {
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

        $worksheetID = 0;
        foreach ($gnmXML->Sheets->Sheet as $sheet) {
            $worksheetName = (string) $sheet->Name;
            if ((isset($this->loadSheetsOnly)) && (!in_array($worksheetName, $this->loadSheetsOnly))) {
                continue;
            }

            $maxRow = $maxCol = 0;

            // Create new Worksheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($worksheetID);
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);

            if ((!$this->readDataOnly) && (isset($sheet->PrintInformation))) {
                if (isset($sheet->PrintInformation->Margins)) {
                    foreach ($sheet->PrintInformation->Margins->children('gnm', true) as $key => $margin) {
                        $marginAttributes = $margin->attributes();
                        $marginSize = 72 / 100; //    Default
                        switch ($marginAttributes['PrefUnit']) {
                            case 'mm':
                                $marginSize = (int) ($marginAttributes['Points']) / 100;

                                break;
                        }
                        switch ($key) {
                            case 'top':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setTop($marginSize);

                                break;
                            case 'bottom':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setBottom($marginSize);

                                break;
                            case 'left':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setLeft($marginSize);

                                break;
                            case 'right':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setRight($marginSize);

                                break;
                            case 'header':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setHeader($marginSize);

                                break;
                            case 'footer':
                                $spreadsheet->getActiveSheet()->getPageMargins()->setFooter($marginSize);

                                break;
                        }
                    }
                }
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
                    switch ($ValueType) {
                        case '10':        //    NULL
                            $type = DataType::TYPE_NULL;

                            break;
                        case '20':        //    Boolean
                            $type = DataType::TYPE_BOOL;
                            $cell = $cell == 'TRUE';

                            break;
                        case '30':        //    Integer
                            $cell = (int) $cell;
                            // Excel 2007+ doesn't differentiate between integer and float, so set the value and dropthru to the next (numeric) case
                            // no break
                        case '40':        //    Float
                            $type = DataType::TYPE_NUMERIC;

                            break;
                        case '50':        //    Error
                            $type = DataType::TYPE_ERROR;

                            break;
                        case '60':        //    String
                            $type = DataType::TYPE_STRING;

                            break;
                        case '70':        //    Cell Range
                        case '80':        //    Array
                    }
                }
                $spreadsheet->getActiveSheet()->getCell($column . $row)->setValueExplicit($cell, $type);
            }

            if ((!$this->readDataOnly) && (isset($sheet->Objects))) {
                foreach ($sheet->Objects->children('gnm', true) as $key => $comment) {
                    $commentAttributes = $comment->attributes();
                    //    Only comment objects are handled at the moment
                    if ($commentAttributes->Text) {
                        $spreadsheet->getActiveSheet()->getComment((string) $commentAttributes->ObjectBound)->setAuthor((string) $commentAttributes->Author)->setText($this->parseRichText((string) $commentAttributes->Text));
                    }
                }
            }
            foreach ($sheet->Styles->StyleRegion as $styleRegion) {
                $styleAttributes = $styleRegion->attributes();
                if (($styleAttributes['startRow'] <= $maxRow) &&
                    ($styleAttributes['startCol'] <= $maxCol)) {
                    $startColumn = Coordinate::stringFromColumnIndex((int) $styleAttributes['startCol'] + 1);
                    $startRow = $styleAttributes['startRow'] + 1;

                    $endColumn = ($styleAttributes['endCol'] > $maxCol) ? $maxCol : (int) $styleAttributes['endCol'];
                    $endColumn = Coordinate::stringFromColumnIndex($endColumn + 1);
                    $endRow = ($styleAttributes['endRow'] > $maxRow) ? $maxRow : $styleAttributes['endRow'];
                    ++$endRow;
                    $cellRange = $startColumn . $startRow . ':' . $endColumn . $endRow;

                    $styleAttributes = $styleRegion->Style->attributes();

                    //    We still set the number format mask for date/time values, even if readDataOnly is true
                    if ((!$this->readDataOnly) ||
                        (Date::isDateTimeFormatCode((string) $styleAttributes['Format']))) {
                        $styleArray = [];
                        $styleArray['numberFormat']['formatCode'] = (string) $styleAttributes['Format'];
                        //    If readDataOnly is false, we set all formatting information
                        if (!$this->readDataOnly) {
                            switch ($styleAttributes['HAlign']) {
                                case '1':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_GENERAL;

                                    break;
                                case '2':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_LEFT;

                                    break;
                                case '4':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_RIGHT;

                                    break;
                                case '8':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_CENTER;

                                    break;
                                case '16':
                                case '64':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_CENTER_CONTINUOUS;

                                    break;
                                case '32':
                                    $styleArray['alignment']['horizontal'] = Alignment::HORIZONTAL_JUSTIFY;

                                    break;
                            }

                            switch ($styleAttributes['VAlign']) {
                                case '1':
                                    $styleArray['alignment']['vertical'] = Alignment::VERTICAL_TOP;

                                    break;
                                case '2':
                                    $styleArray['alignment']['vertical'] = Alignment::VERTICAL_BOTTOM;

                                    break;
                                case '4':
                                    $styleArray['alignment']['vertical'] = Alignment::VERTICAL_CENTER;

                                    break;
                                case '8':
                                    $styleArray['alignment']['vertical'] = Alignment::VERTICAL_JUSTIFY;

                                    break;
                            }

                            $styleArray['alignment']['wrapText'] = $styleAttributes['WrapText'] == '1';
                            $styleArray['alignment']['shrinkToFit'] = $styleAttributes['ShrinkToFit'] == '1';
                            $styleArray['alignment']['indent'] = ((int) ($styleAttributes['Indent']) > 0) ? $styleAttributes['indent'] : 0;

                            $RGB = self::parseGnumericColour($styleAttributes['Fore']);
                            $styleArray['font']['color']['rgb'] = $RGB;
                            $RGB = self::parseGnumericColour($styleAttributes['Back']);
                            $shade = $styleAttributes['Shade'];
                            if (($RGB != '000000') || ($shade != '0')) {
                                $styleArray['fill']['color']['rgb'] = $styleArray['fill']['startColor']['rgb'] = $RGB;
                                $RGB2 = self::parseGnumericColour($styleAttributes['PatternColor']);
                                $styleArray['fill']['endColor']['rgb'] = $RGB2;
                                switch ($shade) {
                                    case '1':
                                        $styleArray['fill']['fillType'] = Fill::FILL_SOLID;

                                        break;
                                    case '2':
                                        $styleArray['fill']['fillType'] = Fill::FILL_GRADIENT_LINEAR;

                                        break;
                                    case '3':
                                        $styleArray['fill']['fillType'] = Fill::FILL_GRADIENT_PATH;

                                        break;
                                    case '4':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKDOWN;

                                        break;
                                    case '5':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKGRAY;

                                        break;
                                    case '6':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKGRID;

                                        break;
                                    case '7':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKHORIZONTAL;

                                        break;
                                    case '8':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKTRELLIS;

                                        break;
                                    case '9':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKUP;

                                        break;
                                    case '10':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_DARKVERTICAL;

                                        break;
                                    case '11':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_GRAY0625;

                                        break;
                                    case '12':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_GRAY125;

                                        break;
                                    case '13':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTDOWN;

                                        break;
                                    case '14':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTGRAY;

                                        break;
                                    case '15':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTGRID;

                                        break;
                                    case '16':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTHORIZONTAL;

                                        break;
                                    case '17':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTTRELLIS;

                                        break;
                                    case '18':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTUP;

                                        break;
                                    case '19':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_LIGHTVERTICAL;

                                        break;
                                    case '20':
                                        $styleArray['fill']['fillType'] = Fill::FILL_PATTERN_MEDIUMGRAY;

                                        break;
                                }
                            }

                            $fontAttributes = $styleRegion->Style->Font->attributes();
                            $styleArray['font']['name'] = (string) $styleRegion->Style->Font;
                            $styleArray['font']['size'] = (int) ($fontAttributes['Unit']);
                            $styleArray['font']['bold'] = $fontAttributes['Bold'] == '1';
                            $styleArray['font']['italic'] = $fontAttributes['Italic'] == '1';
                            $styleArray['font']['strikethrough'] = $fontAttributes['StrikeThrough'] == '1';
                            switch ($fontAttributes['Underline']) {
                                case '1':
                                    $styleArray['font']['underline'] = Font::UNDERLINE_SINGLE;

                                    break;
                                case '2':
                                    $styleArray['font']['underline'] = Font::UNDERLINE_DOUBLE;

                                    break;
                                case '3':
                                    $styleArray['font']['underline'] = Font::UNDERLINE_SINGLEACCOUNTING;

                                    break;
                                case '4':
                                    $styleArray['font']['underline'] = Font::UNDERLINE_DOUBLEACCOUNTING;

                                    break;
                                default:
                                    $styleArray['font']['underline'] = Font::UNDERLINE_NONE;

                                    break;
                            }
                            switch ($fontAttributes['Script']) {
                                case '1':
                                    $styleArray['font']['superscript'] = true;

                                    break;
                                case '-1':
                                    $styleArray['font']['subscript'] = true;

                                    break;
                            }

                            if (isset($styleRegion->Style->StyleBorder)) {
                                if (isset($styleRegion->Style->StyleBorder->Top)) {
                                    $styleArray['borders']['top'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Top->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Bottom)) {
                                    $styleArray['borders']['bottom'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Bottom->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Left)) {
                                    $styleArray['borders']['left'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Left->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Right)) {
                                    $styleArray['borders']['right'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Right->attributes());
                                }
                                if ((isset($styleRegion->Style->StyleBorder->Diagonal)) && (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}))) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_BOTH;
                                } elseif (isset($styleRegion->Style->StyleBorder->Diagonal)) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_UP;
                                } elseif (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'})) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}->attributes());
                                    $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_DOWN;
                                }
                            }
                            if (isset($styleRegion->Style->HyperLink)) {
                                //    TO DO
                                $hyperlink = $styleRegion->Style->HyperLink->attributes();
                            }
                        }
                        $spreadsheet->getActiveSheet()->getStyle($cellRange)->applyFromArray($styleArray);
                    }
                }
            }

            if ((!$this->readDataOnly) && (isset($sheet->Cols))) {
                //    Column Widths
                $columnAttributes = $sheet->Cols->attributes();
                $defaultWidth = $columnAttributes['DefaultSizePts'] / 5.4;
                $c = 0;
                foreach ($sheet->Cols->ColInfo as $columnOverride) {
                    $columnAttributes = $columnOverride->attributes();
                    $column = $columnAttributes['No'];
                    $columnWidth = $columnAttributes['Unit'] / 5.4;
                    $hidden = (isset($columnAttributes['Hidden'])) && ($columnAttributes['Hidden'] == '1');
                    $columnCount = (isset($columnAttributes['Count'])) ? $columnAttributes['Count'] : 1;
                    while ($c < $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($defaultWidth);
                        ++$c;
                    }
                    while (($c < ($column + $columnCount)) && ($c <= $maxCol)) {
                        $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($columnWidth);
                        if ($hidden) {
                            $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setVisible(false);
                        }
                        ++$c;
                    }
                }
                while ($c <= $maxCol) {
                    $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth($defaultWidth);
                    ++$c;
                }
            }

            if ((!$this->readDataOnly) && (isset($sheet->Rows))) {
                //    Row Heights
                $rowAttributes = $sheet->Rows->attributes();
                $defaultHeight = $rowAttributes['DefaultSizePts'];
                $r = 0;

                foreach ($sheet->Rows->RowInfo as $rowOverride) {
                    $rowAttributes = $rowOverride->attributes();
                    $row = $rowAttributes['No'];
                    $rowHeight = $rowAttributes['Unit'];
                    $hidden = (isset($rowAttributes['Hidden'])) && ($rowAttributes['Hidden'] == '1');
                    $rowCount = (isset($rowAttributes['Count'])) ? $rowAttributes['Count'] : 1;
                    while ($r < $row) {
                        ++$r;
                        $spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                    }
                    while (($r < ($row + $rowCount)) && ($r < $maxRow)) {
                        ++$r;
                        $spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($rowHeight);
                        if ($hidden) {
                            $spreadsheet->getActiveSheet()->getRowDimension($r)->setVisible(false);
                        }
                    }
                }
                while ($r < $maxRow) {
                    ++$r;
                    $spreadsheet->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                }
            }

            //    Handle Merged Cells in this worksheet
            if (isset($sheet->MergedRegions)) {
                foreach ($sheet->MergedRegions->Merge as $mergeCells) {
                    if (strpos($mergeCells, ':') !== false) {
                        $spreadsheet->getActiveSheet()->mergeCells($mergeCells);
                    }
                }
            }

            ++$worksheetID;
        }

        //    Loop through definedNames (global named ranges)
        if (isset($gnmXML->Names)) {
            foreach ($gnmXML->Names->Name as $namedRange) {
                $name = (string) $namedRange->name;
                $range = (string) $namedRange->value;
                if (stripos($range, '#REF!') !== false) {
                    continue;
                }

                $range = Worksheet::extractSheetTitle($range, true);
                $range[0] = trim($range[0], "'");
                if ($worksheet = $spreadsheet->getSheetByName($range[0])) {
                    $extractedRange = str_replace('$', '', $range[1]);
                    $spreadsheet->addNamedRange(new NamedRange($name, $worksheet, $extractedRange));
                }
            }
        }

        // Return
        return $spreadsheet;
    }

    private static function parseBorderAttributes($borderAttributes)
    {
        $styleArray = [];
        if (isset($borderAttributes['Color'])) {
            $styleArray['color']['rgb'] = self::parseGnumericColour($borderAttributes['Color']);
        }

        switch ($borderAttributes['Style']) {
            case '0':
                $styleArray['borderStyle'] = Border::BORDER_NONE;

                break;
            case '1':
                $styleArray['borderStyle'] = Border::BORDER_THIN;

                break;
            case '2':
                $styleArray['borderStyle'] = Border::BORDER_MEDIUM;

                break;
            case '3':
                $styleArray['borderStyle'] = Border::BORDER_SLANTDASHDOT;

                break;
            case '4':
                $styleArray['borderStyle'] = Border::BORDER_DASHED;

                break;
            case '5':
                $styleArray['borderStyle'] = Border::BORDER_THICK;

                break;
            case '6':
                $styleArray['borderStyle'] = Border::BORDER_DOUBLE;

                break;
            case '7':
                $styleArray['borderStyle'] = Border::BORDER_DOTTED;

                break;
            case '8':
                $styleArray['borderStyle'] = Border::BORDER_MEDIUMDASHED;

                break;
            case '9':
                $styleArray['borderStyle'] = Border::BORDER_DASHDOT;

                break;
            case '10':
                $styleArray['borderStyle'] = Border::BORDER_MEDIUMDASHDOT;

                break;
            case '11':
                $styleArray['borderStyle'] = Border::BORDER_DASHDOTDOT;

                break;
            case '12':
                $styleArray['borderStyle'] = Border::BORDER_MEDIUMDASHDOTDOT;

                break;
            case '13':
                $styleArray['borderStyle'] = Border::BORDER_MEDIUMDASHDOTDOT;

                break;
        }

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
}
