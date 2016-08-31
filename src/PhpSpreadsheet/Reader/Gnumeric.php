<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
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
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Gnumeric extends BaseReader implements IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $styles = [];

    /**
     * Shared Expressions
     *
     * @var array
     */
    private $expressions = [];

    private $referenceHelper = null;

    /**
     * Create a new Gnumeric
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
        $this->referenceHelper = \PhpOffice\PhpSpreadsheet\ReferenceHelper::getInstance();
    }

    /**
     * Can the current IReader read the file?
     *
     * @param     string         $pFilename
     * @throws Exception
     * @return     bool
     */
    public function canRead($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        // Check if gzlib functions are available
        if (!function_exists('gzread')) {
            throw new Exception('gzlib library is not enabled');
        }

        // Read signature data (first 3 bytes)
        $fh = fopen($pFilename, 'r');
        $data = fread($fh, 2);
        fclose($fh);

        if ($data != chr(0x1F) . chr(0x8B)) {
            return false;
        }

        return true;
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object
     *
     * @param   string         $pFilename
     * @throws  Exception
     */
    public function listWorksheetNames($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $xml = new XMLReader();
        $xml->xml($this->securityScanFile('compress.zlib://' . realpath($pFilename)), null, \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions());
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
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
     *
     * @param   string     $pFilename
     * @throws   Exception
     */
    public function listWorksheetInfo($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $xml = new XMLReader();
        $xml->xml($this->securityScanFile('compress.zlib://' . realpath($pFilename)), null, \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions());
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
                $tmpInfo['lastColumnLetter'] = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
                $worksheetInfo[] = $tmpInfo;
            }
        }

        return $worksheetInfo;
    }

    /**
     * @param string $filename
     */
    private function gzfileGetContents($filename)
    {
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
     * Loads Spreadsheet from file
     *
     * @param     string         $pFilename
     * @throws     Exception
     * @return     Spreadsheet
     */
    public function load($pFilename)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    /**
     * Loads from file into Spreadsheet instance
     *
     * @param     string         $pFilename
     * @param    Spreadsheet    $spreadsheet
     * @throws     Exception
     * @return     Spreadsheet
     */
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');

        $gFileData = $this->gzfileGetContents($pFilename);

        $xml = simplexml_load_string($this->securityScan($gFileData), 'SimpleXMLElement', \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions());
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
                            list(, $attrName) = explode(':', $attributes['name']);
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
            $spreadsheet->getActiveSheet()->setTitle($worksheetName, false);

            if ((!$this->readDataOnly) && (isset($sheet->PrintInformation))) {
                if (isset($sheet->PrintInformation->Margins)) {
                    foreach ($sheet->PrintInformation->Margins->children('gnm', true) as $key => $margin) {
                        $marginAttributes = $margin->attributes();
                        $marginSize = 72 / 100; //    Default
                        switch ($marginAttributes['PrefUnit']) {
                            case 'mm':
                                $marginSize = intval($marginAttributes['Points']) / 100;
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

                $column = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($column);

                // Read cell?
                if ($this->getReadFilter() !== null) {
                    if (!$this->getReadFilter()->readCell($column, $row, $worksheetName)) {
                        continue;
                    }
                }

                $ValueType = $cellAttributes->ValueType;
                $ExprID = (string) $cellAttributes->ExprID;
                $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA;
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
                    $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA;
                } else {
                    switch ($ValueType) {
                        case '10':        //    NULL
                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL;
                            break;
                        case '20':        //    Boolean
                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_BOOL;
                            $cell = ($cell == 'TRUE') ? true : false;
                            break;
                        case '30':        //    Integer
                            $cell = intval($cell);
                            // Excel 2007+ doesn't differentiate between integer and float, so set the value and dropthru to the next (numeric) case
                        case '40':        //    Float
                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                            break;
                        case '50':        //    Error
                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_ERROR;
                            break;
                        case '60':        //    String
                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
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
                    $startColumn = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((int) $styleAttributes['startCol']);
                    $startRow = $styleAttributes['startRow'] + 1;

                    $endColumn = ($styleAttributes['endCol'] > $maxCol) ? $maxCol : (int) $styleAttributes['endCol'];
                    $endColumn = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($endColumn);
                    $endRow = ($styleAttributes['endRow'] > $maxRow) ? $maxRow : $styleAttributes['endRow'];
                    $endRow += 1;
                    $cellRange = $startColumn . $startRow . ':' . $endColumn . $endRow;

                    $styleAttributes = $styleRegion->Style->attributes();

                    //    We still set the number format mask for date/time values, even if readDataOnly is true
                    if ((!$this->readDataOnly) ||
                        (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTimeFormatCode((string) $styleAttributes['Format']))) {
                        $styleArray = [];
                        $styleArray['numberformat']['code'] = (string) $styleAttributes['Format'];
                        //    If readDataOnly is false, we set all formatting information
                        if (!$this->readDataOnly) {
                            switch ($styleAttributes['HAlign']) {
                                case '1':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL;
                                    break;
                                case '2':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                                    break;
                                case '4':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                                    break;
                                case '8':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                                    break;
                                case '16':
                                case '64':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS;
                                    break;
                                case '32':
                                    $styleArray['alignment']['horizontal'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY;
                                    break;
                            }

                            switch ($styleAttributes['VAlign']) {
                                case '1':
                                    $styleArray['alignment']['vertical'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP;
                                    break;
                                case '2':
                                    $styleArray['alignment']['vertical'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM;
                                    break;
                                case '4':
                                    $styleArray['alignment']['vertical'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                                    break;
                                case '8':
                                    $styleArray['alignment']['vertical'] = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY;
                                    break;
                            }

                            $styleArray['alignment']['wrap'] = ($styleAttributes['WrapText'] == '1') ? true : false;
                            $styleArray['alignment']['shrinkToFit'] = ($styleAttributes['ShrinkToFit'] == '1') ? true : false;
                            $styleArray['alignment']['indent'] = (intval($styleAttributes['Indent']) > 0) ? $styleAttributes['indent'] : 0;

                            $RGB = self::parseGnumericColour($styleAttributes['Fore']);
                            $styleArray['font']['color']['rgb'] = $RGB;
                            $RGB = self::parseGnumericColour($styleAttributes['Back']);
                            $shade = $styleAttributes['Shade'];
                            if (($RGB != '000000') || ($shade != '0')) {
                                $styleArray['fill']['color']['rgb'] = $styleArray['fill']['startcolor']['rgb'] = $RGB;
                                $RGB2 = self::parseGnumericColour($styleAttributes['PatternColor']);
                                $styleArray['fill']['endcolor']['rgb'] = $RGB2;
                                switch ($shade) {
                                    case '1':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
                                        break;
                                    case '2':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR;
                                        break;
                                    case '3':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_PATH;
                                        break;
                                    case '4':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKDOWN;
                                        break;
                                    case '5':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKGRAY;
                                        break;
                                    case '6':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKGRID;
                                        break;
                                    case '7':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKHORIZONTAL;
                                        break;
                                    case '8':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKTRELLIS;
                                        break;
                                    case '9':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKUP;
                                        break;
                                    case '10':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_DARKVERTICAL;
                                        break;
                                    case '11':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_GRAY0625;
                                        break;
                                    case '12':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_GRAY125;
                                        break;
                                    case '13':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTDOWN;
                                        break;
                                    case '14':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTGRAY;
                                        break;
                                    case '15':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTGRID;
                                        break;
                                    case '16':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTHORIZONTAL;
                                        break;
                                    case '17':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTTRELLIS;
                                        break;
                                    case '18':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTUP;
                                        break;
                                    case '19':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTVERTICAL;
                                        break;
                                    case '20':
                                        $styleArray['fill']['type'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_MEDIUMGRAY;
                                        break;
                                }
                            }

                            $fontAttributes = $styleRegion->Style->Font->attributes();
                            $styleArray['font']['name'] = (string) $styleRegion->Style->Font;
                            $styleArray['font']['size'] = intval($fontAttributes['Unit']);
                            $styleArray['font']['bold'] = ($fontAttributes['Bold'] == '1') ? true : false;
                            $styleArray['font']['italic'] = ($fontAttributes['Italic'] == '1') ? true : false;
                            $styleArray['font']['strike'] = ($fontAttributes['StrikeThrough'] == '1') ? true : false;
                            switch ($fontAttributes['Underline']) {
                                case '1':
                                    $styleArray['font']['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE;
                                    break;
                                case '2':
                                    $styleArray['font']['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE;
                                    break;
                                case '3':
                                    $styleArray['font']['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLEACCOUNTING;
                                    break;
                                case '4':
                                    $styleArray['font']['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING;
                                    break;
                                default:
                                    $styleArray['font']['underline'] = \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE;
                                    break;
                            }
                            switch ($fontAttributes['Script']) {
                                case '1':
                                    $styleArray['font']['superScript'] = true;
                                    break;
                                case '-1':
                                    $styleArray['font']['subScript'] = true;
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
                                    $styleArray['borders']['diagonaldirection'] = \PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_BOTH;
                                } elseif (isset($styleRegion->Style->StyleBorder->Diagonal)) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonaldirection'] = \PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_UP;
                                } elseif (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'})) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}->attributes());
                                    $styleArray['borders']['diagonaldirection'] = \PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_DOWN;
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
                    $hidden = ((isset($columnAttributes['Hidden'])) && ($columnAttributes['Hidden'] == '1')) ? true : false;
                    $columnCount = (isset($columnAttributes['Count'])) ? $columnAttributes['Count'] : 1;
                    while ($c < $column) {
                        $spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
                        ++$c;
                    }
                    while (($c < ($column + $columnCount)) && ($c <= $maxCol)) {
                        $spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($c))->setWidth($columnWidth);
                        if ($hidden) {
                            $spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($c))->setVisible(false);
                        }
                        ++$c;
                    }
                }
                while ($c <= $maxCol) {
                    $spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
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
                    $hidden = ((isset($rowAttributes['Hidden'])) && ($rowAttributes['Hidden'] == '1')) ? true : false;
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

                $range = explode('!', $range);
                $range[0] = trim($range[0], "'");
                if ($worksheet = $spreadsheet->getSheetByName($range[0])) {
                    $extractedRange = str_replace('$', '', $range[1]);
                    $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange($name, $worksheet, $extractedRange));
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
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
                break;
            case '1':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
                break;
            case '2':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM;
                break;
            case '3':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_SLANTDASHDOT;
                break;
            case '4':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHED;
                break;
            case '5':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK;
                break;
            case '6':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE;
                break;
            case '7':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED;
                break;
            case '8':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHED;
                break;
            case '9':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOT;
                break;
            case '10':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOT;
                break;
            case '11':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOTDOT;
                break;
            case '12':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOTDOT;
                break;
            case '13':
                $styleArray['style'] = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOTDOT;
                break;
        }

        return $styleArray;
    }

    private function parseRichText($is = '')
    {
        $value = new \PhpOffice\PhpSpreadsheet\RichText();
        $value->createText($is);

        return $value;
    }

    private static function parseGnumericColour($gnmColour)
    {
        list($gnmR, $gnmG, $gnmB) = explode(':', $gnmColour);
        $gnmR = substr(str_pad($gnmR, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmG = substr(str_pad($gnmG, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmB = substr(str_pad($gnmB, 4, '0', STR_PAD_RIGHT), 0, 2);

        return $gnmR . $gnmG . $gnmB;
    }
}
