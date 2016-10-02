<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DateTime;
use DateTimeZone;

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
class OOCalc extends BaseReader implements IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $styles = [];

    /**
     * Create a new OOCalc Reader instance
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
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

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        // Check if zip class exists
//        if (!class_exists($zipClass, false)) {
//            throw new Exception($zipClass . " library is not enabled");
//        }

        $mimeType = 'UNKNOWN';
        // Load file
        $zip = new $zipClass();
        if ($zip->open($pFilename) === true) {
            // check if it is an OOXML archive
            $stat = $zip->statName('mimetype');
            if ($stat && ($stat['size'] <= 255)) {
                $mimeType = $zip->getFromName($stat['name']);
            } elseif ($stat = $zip->statName('META-INF/manifest.xml')) {
                $xml = simplexml_load_string(
                    $this->securityScan($zip->getFromName('META-INF/manifest.xml')),
                    'SimpleXMLElement',
                    \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                );
                $namespacesContent = $xml->getNamespaces(true);
                if (isset($namespacesContent['manifest'])) {
                    $manifest = $xml->children($namespacesContent['manifest']);
                    foreach ($manifest as $manifestDataSet) {
                        $manifestAttributes = $manifestDataSet->attributes($namespacesContent['manifest']);
                        if ($manifestAttributes->{'full-path'} == '/') {
                            $mimeType = (string) $manifestAttributes->{'media-type'};
                            break;
                        }
                    }
                }
            }

            $zip->close();

            return $mimeType === 'application/vnd.oasis.opendocument.spreadsheet';
        }

        return false;
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PhpSpreadsheet object
     *
     * @param     string         $pFilename
     * @throws     Exception
     */
    public function listWorksheetNames($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $worksheetNames = [];

        $xml = new XMLReader();
        $res = $xml->xml(
            $this->securityScanFile('zip://' . realpath($pFilename) . '#content.xml'),
            null,
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        //    Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            //    Quickly jump through to the office:body node
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            //    Now read each node until we find our first table:table node
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    //    Loop through each table:table node reading the table:name attribute for each worksheet name
                    do {
                        $worksheetNames[] = $xml->getAttribute('table:name');
                        $xml->next();
                    } while ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
                }
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

        $worksheetInfo = [];

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $xml = new XMLReader();
        $res = $xml->xml(
            $this->securityScanFile('zip://' . realpath($pFilename) . '#content.xml'),
            null,
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        //    Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            //    Quickly jump through to the office:body node
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
                //    Now read each node until we find our first table:table node
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    $worksheetNames[] = $xml->getAttribute('table:name');

                    $tmpInfo = [
                        'worksheetName' => $xml->getAttribute('table:name'),
                        'lastColumnLetter' => 'A',
                        'lastColumnIndex' => 0,
                        'totalRows' => 0,
                        'totalColumns' => 0,
                    ];

                    //    Loop through each child node of the table:table element reading
                    $currCells = 0;
                    do {
                        $xml->read();
                        if ($xml->name == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
                            $rowspan = $xml->getAttribute('table:number-rows-repeated');
                            $rowspan = empty($rowspan) ? 1 : $rowspan;
                            $tmpInfo['totalRows'] += $rowspan;
                            $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                            $currCells = 0;
                            //    Step into the row
                            $xml->read();
                            do {
                                if ($xml->name == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    if (!$xml->isEmptyElement) {
                                        ++$currCells;
                                        $xml->next();
                                    } else {
                                        $xml->read();
                                    }
                                } elseif ($xml->name == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    $mergeSize = $xml->getAttribute('table:number-columns-repeated');
                                    $currCells += $mergeSize;
                                    $xml->read();
                                }
                            } while ($xml->name != 'table:table-row');
                        }
                    } while ($xml->name != 'table:table');

                    $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                    $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                    $tmpInfo['lastColumnLetter'] = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
                    $worksheetInfo[] = $tmpInfo;
                }
            }
        }

        return $worksheetInfo;
    }

    /**
     * Loads PhpSpreadsheet from file
     *
     * @param     string         $pFilename
     * @throws     Exception
     * @return     \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function load($pFilename)
    {
        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

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
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance
     *
     * @param     string         $pFilename
     * @param    \PhpOffice\PhpSpreadsheet\Spreadsheet    $spreadsheet
     * @throws     Exception
     * @return     \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function loadIntoExisting($pFilename, \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new \DateTimeZone('UTC');

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $xml = simplexml_load_string(
            $this->securityScan($zip->getFromName('meta.xml')),
            'SimpleXMLElement',
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        $namespacesMeta = $xml->getNamespaces(true);

        $docProps = $spreadsheet->getProperties();
        $officeProperty = $xml->children($namespacesMeta['office']);
        foreach ($officeProperty as $officePropertyData) {
            $officePropertyDC = [];
            if (isset($namespacesMeta['dc'])) {
                $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
            }
            foreach ($officePropertyDC as $propertyName => $propertyValue) {
                $propertyValue = (string) $propertyValue;
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
            $officePropertyMeta = [];
            if (isset($namespacesMeta['dc'])) {
                $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
            }
            foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                $propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);
                $propertyValue = (string) $propertyValue;
                switch ($propertyName) {
                    case 'initial-creator':
                        $docProps->setCreator($propertyValue);
                        break;
                    case 'keyword':
                        $docProps->setKeywords($propertyValue);
                        break;
                    case 'creation-date':
                        $creationDate = strtotime($propertyValue);
                        $docProps->setCreated($creationDate);
                        break;
                    case 'user-defined':
                        $propertyValueType = \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_STRING;
                        foreach ($propertyValueAttributes as $key => $value) {
                            if ($key == 'name') {
                                $propertyValueName = (string) $value;
                            } elseif ($key == 'value-type') {
                                switch ($value) {
                                    case 'date':
                                        $propertyValue = \PhpOffice\PhpSpreadsheet\Document\Properties::convertProperty($propertyValue, 'date');
                                        $propertyValueType = \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_DATE;
                                        break;
                                    case 'boolean':
                                        $propertyValue = \PhpOffice\PhpSpreadsheet\Document\Properties::convertProperty($propertyValue, 'bool');
                                        $propertyValueType = \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_BOOLEAN;
                                        break;
                                    case 'float':
                                        $propertyValue = \PhpOffice\PhpSpreadsheet\Document\Properties::convertProperty($propertyValue, 'r4');
                                        $propertyValueType = \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_FLOAT;
                                        break;
                                    default:
                                        $propertyValueType = \PhpOffice\PhpSpreadsheet\Document\Properties::PROPERTY_TYPE_STRING;
                                }
                            }
                        }
                        $docProps->setCustomProperty($propertyValueName, $propertyValue, $propertyValueType);
                        break;
                }
            }
        }

        $xml = simplexml_load_string(
            $this->securityScan($zip->getFromName('content.xml')),
            'SimpleXMLElement',
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        $namespacesContent = $xml->getNamespaces(true);

        $workbook = $xml->children($namespacesContent['office']);
        foreach ($workbook->body->spreadsheet as $workbookData) {
            $workbookData = $workbookData->children($namespacesContent['table']);
            $worksheetID = 0;
            foreach ($workbookData->table as $worksheetDataSet) {
                $worksheetData = $worksheetDataSet->children($namespacesContent['table']);
                $worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
                if ((isset($this->loadSheetsOnly)) && (isset($worksheetDataAttributes['name'])) &&
                    (!in_array($worksheetDataAttributes['name'], $this->loadSheetsOnly))) {
                    continue;
                }

                // Create new Worksheet
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($worksheetID);
                if (isset($worksheetDataAttributes['name'])) {
                    $worksheetName = (string) $worksheetDataAttributes['name'];
                    //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                    //        formula cells... during the load, all formulae should be correct, and we're simply
                    //        bringing the worksheet name in line with the formula, not the reverse
                    $spreadsheet->getActiveSheet()->setTitle($worksheetName, false);
                }

                $rowID = 1;
                foreach ($worksheetData as $key => $rowData) {
                    switch ($key) {
                        case 'table-header-rows':
                            foreach ($rowData as $keyRowData => $cellData) {
                                $rowData = $cellData;
                                break;
                            }
                            break;
                        case 'table-row':
                            $rowDataTableAttributes = $rowData->attributes($namespacesContent['table']);
                            $rowRepeats = (isset($rowDataTableAttributes['number-rows-repeated'])) ? $rowDataTableAttributes['number-rows-repeated'] : 1;
                            $columnID = 'A';
                            foreach ($rowData as $key => $cellData) {
                                if ($this->getReadFilter() !== null) {
                                    if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                        continue;
                                    }
                                }

                                $cellDataText = (isset($namespacesContent['text'])) ? $cellData->children($namespacesContent['text']) : '';
                                $cellDataOffice = $cellData->children($namespacesContent['office']);
                                $cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
                                $cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);

                                $type = $formatting = $hyperlink = null;
                                $hasCalculatedValue = false;
                                $cellDataFormula = '';
                                if (isset($cellDataTableAttributes['formula'])) {
                                    $cellDataFormula = $cellDataTableAttributes['formula'];
                                    $hasCalculatedValue = true;
                                }

                                if (isset($cellDataOffice->annotation)) {
                                    $annotationText = $cellDataOffice->annotation->children($namespacesContent['text']);
                                    $textArray = [];
                                    foreach ($annotationText as $t) {
                                        if (isset($t->span)) {
                                            foreach ($t->span as $text) {
                                                $textArray[] = (string) $text;
                                            }
                                        } else {
                                            $textArray[] = (string) $t;
                                        }
                                    }
                                    $text = implode("\n", $textArray);
                                    $spreadsheet->getActiveSheet()->getComment($columnID . $rowID)->setText($this->parseRichText($text));
//                                                                    ->setAuthor( $author )
                                }

                                if (isset($cellDataText->p)) {
                                    // Consolidate if there are multiple p records (maybe with spans as well)
                                    $dataArray = [];
                                    // Text can have multiple text:p and within those, multiple text:span.
                                    // text:p newlines, but text:span does not.
                                    // Also, here we assume there is no text data is span fields are specified, since
                                    // we have no way of knowing proper positioning anyway.
                                    foreach ($cellDataText->p as $pData) {
                                        if (isset($pData->span)) {
                                            // span sections do not newline, so we just create one large string here
                                            $spanSection = '';
                                            foreach ($pData->span as $spanData) {
                                                $spanSection .= $spanData;
                                            }
                                            array_push($dataArray, $spanSection);
                                        } elseif (isset($pData->a)) {
                                            //Reading the hyperlinks in p
                                            array_push($dataArray, $pData->a);
                                        } else {
                                            array_push($dataArray, $pData);
                                        }
                                    }
                                    $allCellDataText = implode($dataArray, "\n");

                                    switch ($cellDataOfficeAttributes['value-type']) {
                                        case 'string':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
                                            $dataValue = $allCellDataText;
                                            if (isset($dataValue->a)) {
                                                $dataValue = $dataValue->a;
                                                $cellXLinkAttributes = $dataValue->attributes($namespacesContent['xlink']);
                                                $hyperlink = $cellXLinkAttributes['href'];
                                            }
                                            break;
                                        case 'boolean':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_BOOL;
                                            $dataValue = ($allCellDataText == 'TRUE') ? true : false;
                                            break;
                                        case 'percentage':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (integer) $dataValue;
                                            }
                                            $formatting = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00;
                                            break;
                                        case 'currency':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (integer) $dataValue;
                                            }
                                            $formatting = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
                                            break;
                                        case 'float':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                if ($dataValue == (integer) $dataValue) {
                                                    $dataValue = (integer) $dataValue;
                                                } else {
                                                    $dataValue = (float) $dataValue;
                                                }
                                            }
                                            break;
                                        case 'date':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                                            $dateObj = new DateTime($cellDataOfficeAttributes['date-value'], $GMT);
                                            $dateObj->setTimeZone($timezoneObj);
                                            list($year, $month, $day, $hour, $minute, $second) = explode(' ', $dateObj->format('Y m d H i s'));
                                            $dataValue = \PhpOffice\PhpSpreadsheet\Shared\Date::formattedPHPToExcel($year, $month, $day, $hour, $minute, $second);
                                            if ($dataValue != floor($dataValue)) {
                                                $formatting = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX15 . ' ' . \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4;
                                            } else {
                                                $formatting = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX15;
                                            }
                                            break;
                                        case 'time':
                                            $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                                            $dataValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime('01-01-1970 ' . implode(':', sscanf($cellDataOfficeAttributes['time-value'], 'PT%dH%dM%dS'))));
                                            $formatting = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4;
                                            break;
                                    }
                                } else {
                                    $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL;
                                    $dataValue = null;
                                }

                                if ($hasCalculatedValue) {
                                    $type = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA;
                                    $cellDataFormula = substr($cellDataFormula, strpos($cellDataFormula, ':=') + 1);
                                    $temp = explode('"', $cellDataFormula);
                                    $tKey = false;
                                    foreach ($temp as &$value) {
                                        //    Only replace in alternate array entries (i.e. non-quoted blocks)
                                        if ($tKey = !$tKey) {
                                            $value = preg_replace('/\[([^\.]+)\.([^\.]+):\.([^\.]+)\]/Ui', '$1!$2:$3', $value); //  Cell range reference in another sheet
                                            $value = preg_replace('/\[([^\.]+)\.([^\.]+)\]/Ui', '$1!$2', $value); //  Cell reference in another sheet
                                            $value = preg_replace('/\[\.([^\.]+):\.([^\.]+)\]/Ui', '$1:$2', $value); //  Cell range reference
                                            $value = preg_replace('/\[\.([^\.]+)\]/Ui', '$1', $value); //  Simple cell reference
                                            $value = \PhpOffice\PhpSpreadsheet\Calculation::translateSeparator(';', ',', $value, $inBraces);
                                        }
                                    }
                                    unset($value);
                                    //    Then rebuild the formula string
                                    $cellDataFormula = implode('"', $temp);
                                }

                                $colRepeats = (isset($cellDataTableAttributes['number-columns-repeated'])) ? $cellDataTableAttributes['number-columns-repeated'] : 1;
                                if ($type !== null) {
                                    for ($i = 0; $i < $colRepeats; ++$i) {
                                        if ($i > 0) {
                                            ++$columnID;
                                        }
                                        if ($type !== \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL) {
                                            for ($rowAdjust = 0; $rowAdjust < $rowRepeats; ++$rowAdjust) {
                                                $rID = $rowID + $rowAdjust;
                                                $spreadsheet->getActiveSheet()->getCell($columnID . $rID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $dataValue), $type);
                                                if ($hasCalculatedValue) {
                                                    $spreadsheet->getActiveSheet()->getCell($columnID . $rID)->setCalculatedValue($dataValue);
                                                }
                                                if ($formatting !== null) {
                                                    $spreadsheet->getActiveSheet()->getStyle($columnID . $rID)->getNumberFormat()->setFormatCode($formatting);
                                                } else {
                                                    $spreadsheet->getActiveSheet()->getStyle($columnID . $rID)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);
                                                }
                                                if ($hyperlink !== null) {
                                                    $spreadsheet->getActiveSheet()->getCell($columnID . $rID)->getHyperlink()->setUrl($hyperlink);
                                                }
                                            }
                                        }
                                    }
                                }

                                //    Merged cells
                                if ((isset($cellDataTableAttributes['number-columns-spanned'])) || (isset($cellDataTableAttributes['number-rows-spanned']))) {
                                    if (($type !== \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL) || (!$this->readDataOnly)) {
                                        $columnTo = $columnID;
                                        if (isset($cellDataTableAttributes['number-columns-spanned'])) {
                                            $columnTo = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex(\PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-spanned'] - 2);
                                        }
                                        $rowTo = $rowID;
                                        if (isset($cellDataTableAttributes['number-rows-spanned'])) {
                                            $rowTo = $rowTo + $cellDataTableAttributes['number-rows-spanned'] - 1;
                                        }
                                        $cellRange = $columnID . $rowID . ':' . $columnTo . $rowTo;
                                        $spreadsheet->getActiveSheet()->mergeCells($cellRange);
                                    }
                                }

                                ++$columnID;
                            }
                            $rowID += $rowRepeats;
                            break;
                    }
                }
                ++$worksheetID;
            }
        }

        // Return
        return $spreadsheet;
    }

    private function parseRichText($is = '')
    {
        $value = new \PhpOffice\PhpSpreadsheet\RichText();

        $value->createText($is);

        return $value;
    }
}
