<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use XMLReader;
use ZipArchive;

class Ods extends BaseReader
{
    /**
     * Create a new Ods Reader instance.
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    /**
     * Can the current IReader read the file?
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return bool
     */
    public function canRead($pFilename)
    {
        File::assertFile($pFilename);

        $mimeType = 'UNKNOWN';

        // Load file

        $zip = new ZipArchive();
        if ($zip->open($pFilename) === true) {
            // check if it is an OOXML archive
            $stat = $zip->statName('mimetype');
            if ($stat && ($stat['size'] <= 255)) {
                $mimeType = $zip->getFromName($stat['name']);
            } elseif ($stat = $zip->statName('META-INF/manifest.xml')) {
                $xml = simplexml_load_string(
                    $this->securityScanner->scan($zip->getFromName('META-INF/manifest.xml')),
                    'SimpleXMLElement',
                    Settings::getLibXmlLoaderOptions()
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
     * Reads names of the worksheets from a file, without parsing the whole file to a PhpSpreadsheet object.
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return string[]
     */
    public function listWorksheetNames($pFilename)
    {
        File::assertFile($pFilename);

        $zip = new ZipArchive();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $worksheetNames = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip://' . realpath($pFilename) . '#content.xml'),
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        // Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            // Quickly jump through to the office:body node
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            // Now read each node until we find our first table:table node
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    // Loop through each table:table node reading the table:name attribute for each worksheet name
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
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return array
     */
    public function listWorksheetInfo($pFilename)
    {
        File::assertFile($pFilename);

        $worksheetInfo = [];

        $zip = new ZipArchive();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip://' . realpath($pFilename) . '#content.xml'),
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        // Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            // Quickly jump through to the office:body node
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            // Now read each node until we find our first table:table node
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

                    // Loop through each child node of the table:table element reading
                    $currCells = 0;
                    do {
                        $xml->read();
                        if ($xml->name == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
                            $rowspan = $xml->getAttribute('table:number-rows-repeated');
                            $rowspan = empty($rowspan) ? 1 : $rowspan;
                            $tmpInfo['totalRows'] += $rowspan;
                            $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                            $currCells = 0;
                            // Step into the row
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
                                    $currCells += (int) $mergeSize;
                                    $xml->read();
                                }
                            } while ($xml->name != 'table:table-row');
                        }
                    } while ($xml->name != 'table:table');

                    $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                    $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                    $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
                    $worksheetInfo[] = $tmpInfo;
                }
            }
        }

        return $worksheetInfo;
    }

    /**
     * Loads PhpSpreadsheet from file.
     *
     * @param string $pFilename
     *
     * @throws Exception
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
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     *
     * @param string $pFilename
     * @param Spreadsheet $spreadsheet
     *
     * @throws Exception
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        File::assertFile($pFilename);

        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new \DateTimeZone('UTC');

        $zip = new ZipArchive();
        if (!$zip->open($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }

        // Meta

        $xml = simplexml_load_string(
            $this->securityScanner->scan($zip->getFromName('meta.xml')),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
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
                        $propertyValueType = Properties::PROPERTY_TYPE_STRING;
                        foreach ($propertyValueAttributes as $key => $value) {
                            if ($key == 'name') {
                                $propertyValueName = (string) $value;
                            } elseif ($key == 'value-type') {
                                switch ($value) {
                                    case 'date':
                                        $propertyValue = Properties::convertProperty($propertyValue, 'date');
                                        $propertyValueType = Properties::PROPERTY_TYPE_DATE;

                                        break;
                                    case 'boolean':
                                        $propertyValue = Properties::convertProperty($propertyValue, 'bool');
                                        $propertyValueType = Properties::PROPERTY_TYPE_BOOLEAN;

                                        break;
                                    case 'float':
                                        $propertyValue = Properties::convertProperty($propertyValue, 'r4');
                                        $propertyValueType = Properties::PROPERTY_TYPE_FLOAT;

                                        break;
                                    default:
                                        $propertyValueType = Properties::PROPERTY_TYPE_STRING;
                                }
                            }
                        }
                        $docProps->setCustomProperty($propertyValueName, $propertyValue, $propertyValueType);

                        break;
                }
            }
        }

        // Content

        $dom = new \DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->securityScanner->scan($zip->getFromName('content.xml')),
            Settings::getLibXmlLoaderOptions()
        );

        $officeNs = $dom->lookupNamespaceUri('office');
        $tableNs = $dom->lookupNamespaceUri('table');
        $textNs = $dom->lookupNamespaceUri('text');
        $xlinkNs = $dom->lookupNamespaceUri('xlink');

        $spreadsheets = $dom->getElementsByTagNameNS($officeNs, 'body')
            ->item(0)
            ->getElementsByTagNameNS($officeNs, 'spreadsheet');

        foreach ($spreadsheets as $workbookData) {
            /** @var \DOMElement $workbookData */
            $tables = $workbookData->getElementsByTagNameNS($tableNs, 'table');

            $worksheetID = 0;
            foreach ($tables as $worksheetDataSet) {
                /** @var \DOMElement $worksheetDataSet */
                $worksheetName = $worksheetDataSet->getAttributeNS($tableNs, 'name');

                // Check loadSheetsOnly
                if (isset($this->loadSheetsOnly)
                    && $worksheetName
                    && !in_array($worksheetName, $this->loadSheetsOnly)) {
                    continue;
                }

                // Create sheet
                if ($worksheetID > 0) {
                    $spreadsheet->createSheet(); // First sheet is added by default
                }
                $spreadsheet->setActiveSheetIndex($worksheetID);

                if ($worksheetName) {
                    // Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                    // formula cells... during the load, all formulae should be correct, and we're simply
                    // bringing the worksheet name in line with the formula, not the reverse
                    $spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);
                }

                // Go through every child of table element
                $rowID = 1;
                foreach ($worksheetDataSet->childNodes as $childNode) {
                    /** @var \DOMElement $childNode */

                    // Filter elements which are not under the "table" ns
                    if ($childNode->namespaceURI != $tableNs) {
                        continue;
                    }

                    $key = $childNode->nodeName;

                    // Remove ns from node name
                    if (strpos($key, ':') !== false) {
                        $keyChunks = explode(':', $key);
                        $key = array_pop($keyChunks);
                    }

                    switch ($key) {
                        case 'table-header-rows':
                            /// TODO :: Figure this out. This is only a partial implementation I guess.
                            //          ($rowData it's not used at all and I'm not sure that PHPExcel
                            //          has an API for this)

//                            foreach ($rowData as $keyRowData => $cellData) {
//                                $rowData = $cellData;
//                                break;
//                            }
                            break;
                        case 'table-row':
                            if ($childNode->hasAttributeNS($tableNs, 'number-rows-repeated')) {
                                $rowRepeats = $childNode->getAttributeNS($tableNs, 'number-rows-repeated');
                            } else {
                                $rowRepeats = 1;
                            }

                            $columnID = 'A';
                            foreach ($childNode->childNodes as $key => $cellData) {
                                // @var \DOMElement $cellData

                                if ($this->getReadFilter() !== null) {
                                    if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                        ++$columnID;

                                        continue;
                                    }
                                }

                                // Initialize variables
                                $formatting = $hyperlink = null;
                                $hasCalculatedValue = false;
                                $cellDataFormula = '';

                                if ($cellData->hasAttributeNS($tableNs, 'formula')) {
                                    $cellDataFormula = $cellData->getAttributeNS($tableNs, 'formula');
                                    $hasCalculatedValue = true;
                                }

                                // Annotations
                                $annotation = $cellData->getElementsByTagNameNS($officeNs, 'annotation');

                                if ($annotation->length > 0) {
                                    $textNode = $annotation->item(0)->getElementsByTagNameNS($textNs, 'p');

                                    if ($textNode->length > 0) {
                                        $text = $this->scanElementForText($textNode->item(0));

                                        $spreadsheet->getActiveSheet()
                                            ->getComment($columnID . $rowID)
                                            ->setText($this->parseRichText($text));
//                                                                    ->setAuthor( $author )
                                    }
                                }

                                // Content

                                /** @var \DOMElement[] $paragraphs */
                                $paragraphs = [];

                                foreach ($cellData->childNodes as $item) {
                                    /** @var \DOMElement $item */

                                    // Filter text:p elements
                                    if ($item->nodeName == 'text:p') {
                                        $paragraphs[] = $item;
                                    }
                                }

                                if (count($paragraphs) > 0) {
                                    // Consolidate if there are multiple p records (maybe with spans as well)
                                    $dataArray = [];

                                    // Text can have multiple text:p and within those, multiple text:span.
                                    // text:p newlines, but text:span does not.
                                    // Also, here we assume there is no text data is span fields are specified, since
                                    // we have no way of knowing proper positioning anyway.

                                    foreach ($paragraphs as $pData) {
                                        $dataArray[] = $this->scanElementForText($pData);
                                    }
                                    $allCellDataText = implode($dataArray, "\n");

                                    $type = $cellData->getAttributeNS($officeNs, 'value-type');

                                    switch ($type) {
                                        case 'string':
                                            $type = DataType::TYPE_STRING;
                                            $dataValue = $allCellDataText;

                                            foreach ($paragraphs as $paragraph) {
                                                $link = $paragraph->getElementsByTagNameNS($textNs, 'a');
                                                if ($link->length > 0) {
                                                    $hyperlink = $link->item(0)->getAttributeNS($xlinkNs, 'href');
                                                }
                                            }

                                            break;
                                        case 'boolean':
                                            $type = DataType::TYPE_BOOL;
                                            $dataValue = ($allCellDataText == 'TRUE') ? true : false;

                                            break;
                                        case 'percentage':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = NumberFormat::FORMAT_PERCENTAGE_00;

                                            break;
                                        case 'currency':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;

                                            break;
                                        case 'float':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                if ($dataValue == (int) $dataValue) {
                                                    $dataValue = (int) $dataValue;
                                                } else {
                                                    $dataValue = (float) $dataValue;
                                                }
                                            }

                                            break;
                                        case 'date':
                                            $type = DataType::TYPE_NUMERIC;
                                            $value = $cellData->getAttributeNS($officeNs, 'date-value');

                                            $dateObj = new DateTime($value, $GMT);
                                            $dateObj->setTimeZone($timezoneObj);
                                            list($year, $month, $day, $hour, $minute, $second) = explode(
                                                ' ',
                                                $dateObj->format('Y m d H i s')
                                            );

                                            $dataValue = Date::formattedPHPToExcel(
                                                $year,
                                                $month,
                                                $day,
                                                $hour,
                                                $minute,
                                                $second
                                            );

                                            if ($dataValue != floor($dataValue)) {
                                                $formatting = NumberFormat::FORMAT_DATE_XLSX15
                                                    . ' '
                                                    . NumberFormat::FORMAT_DATE_TIME4;
                                            } else {
                                                $formatting = NumberFormat::FORMAT_DATE_XLSX15;
                                            }

                                            break;
                                        case 'time':
                                            $type = DataType::TYPE_NUMERIC;

                                            $timeValue = $cellData->getAttributeNS($officeNs, 'time-value');

                                            $dataValue = Date::PHPToExcel(
                                                strtotime(
                                                    '01-01-1970 ' . implode(':', sscanf($timeValue, 'PT%dH%dM%dS'))
                                                )
                                            );
                                            $formatting = NumberFormat::FORMAT_DATE_TIME4;

                                            break;
                                        default:
                                            $dataValue = null;
                                    }
                                } else {
                                    $type = DataType::TYPE_NULL;
                                    $dataValue = null;
                                }

                                if ($hasCalculatedValue) {
                                    $type = DataType::TYPE_FORMULA;
                                    $cellDataFormula = substr($cellDataFormula, strpos($cellDataFormula, ':=') + 1);
                                    $temp = explode('"', $cellDataFormula);
                                    $tKey = false;
                                    foreach ($temp as &$value) {
                                        // Only replace in alternate array entries (i.e. non-quoted blocks)
                                        if ($tKey = !$tKey) {
                                            // Cell range reference in another sheet
                                            $value = preg_replace('/\[([^\.]+)\.([^\.]+):\.([^\.]+)\]/U', '$1!$2:$3', $value);

                                            // Cell reference in another sheet
                                            $value = preg_replace('/\[([^\.]+)\.([^\.]+)\]/U', '$1!$2', $value);

                                            // Cell range reference
                                            $value = preg_replace('/\[\.([^\.]+):\.([^\.]+)\]/U', '$1:$2', $value);

                                            // Simple cell reference
                                            $value = preg_replace('/\[\.([^\.]+)\]/U', '$1', $value);

                                            $value = Calculation::translateSeparator(';', ',', $value, $inBraces);
                                        }
                                    }
                                    unset($value);

                                    // Then rebuild the formula string
                                    $cellDataFormula = implode('"', $temp);
                                }

                                if ($cellData->hasAttributeNS($tableNs, 'number-columns-repeated')) {
                                    $colRepeats = (int) $cellData->getAttributeNS($tableNs, 'number-columns-repeated');
                                } else {
                                    $colRepeats = 1;
                                }

                                if ($type !== null) {
                                    for ($i = 0; $i < $colRepeats; ++$i) {
                                        if ($i > 0) {
                                            ++$columnID;
                                        }

                                        if ($type !== DataType::TYPE_NULL) {
                                            for ($rowAdjust = 0; $rowAdjust < $rowRepeats; ++$rowAdjust) {
                                                $rID = $rowID + $rowAdjust;

                                                $cell = $spreadsheet->getActiveSheet()
                                                    ->getCell($columnID . $rID);

                                                // Set value
                                                if ($hasCalculatedValue) {
                                                    $cell->setValueExplicit($cellDataFormula, $type);
                                                } else {
                                                    $cell->setValueExplicit($dataValue, $type);
                                                }

                                                if ($hasCalculatedValue) {
                                                    $cell->setCalculatedValue($dataValue);
                                                }

                                                // Set other properties
                                                if ($formatting !== null) {
                                                    $spreadsheet->getActiveSheet()
                                                        ->getStyle($columnID . $rID)
                                                        ->getNumberFormat()
                                                        ->setFormatCode($formatting);
                                                } else {
                                                    $spreadsheet->getActiveSheet()
                                                        ->getStyle($columnID . $rID)
                                                        ->getNumberFormat()
                                                        ->setFormatCode(NumberFormat::FORMAT_GENERAL);
                                                }

                                                if ($hyperlink !== null) {
                                                    $cell->getHyperlink()
                                                        ->setUrl($hyperlink);
                                                }
                                            }
                                        }
                                    }
                                }

                                // Merged cells
                                if ($cellData->hasAttributeNS($tableNs, 'number-columns-spanned')
                                    || $cellData->hasAttributeNS($tableNs, 'number-rows-spanned')
                                ) {
                                    if (($type !== DataType::TYPE_NULL) || (!$this->readDataOnly)) {
                                        $columnTo = $columnID;

                                        if ($cellData->hasAttributeNS($tableNs, 'number-columns-spanned')) {
                                            $columnIndex = Coordinate::columnIndexFromString($columnID);
                                            $columnIndex += (int) $cellData->getAttributeNS($tableNs, 'number-columns-spanned');
                                            $columnIndex -= 2;

                                            $columnTo = Coordinate::stringFromColumnIndex($columnIndex + 1);
                                        }

                                        $rowTo = $rowID;

                                        if ($cellData->hasAttributeNS($tableNs, 'number-rows-spanned')) {
                                            $rowTo = $rowTo + (int) $cellData->getAttributeNS($tableNs, 'number-rows-spanned') - 1;
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

    /**
     * Recursively scan element.
     *
     * @param \DOMNode $element
     *
     * @return string
     */
    protected function scanElementForText(\DOMNode $element)
    {
        $str = '';
        foreach ($element->childNodes as $child) {
            /** @var \DOMNode $child */
            if ($child->nodeType == XML_TEXT_NODE) {
                $str .= $child->nodeValue;
            } elseif ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'text:s') {
                // It's a space

                // Multiple spaces?
                /** @var \DOMAttr $cAttr */
                $cAttr = $child->attributes->getNamedItem('c');
                if ($cAttr) {
                    $multiplier = (int) $cAttr->nodeValue;
                } else {
                    $multiplier = 1;
                }

                $str .= str_repeat(' ', $multiplier);
            }

            if ($child->hasChildNodes()) {
                $str .= $this->scanElementForText($child);
            }
        }

        return $str;
    }

    /**
     * @param string $is
     *
     * @return RichText
     */
    private function parseRichText($is)
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }
}
