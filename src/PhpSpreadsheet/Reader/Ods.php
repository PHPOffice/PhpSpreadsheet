<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Helper\Dimension as HelperDimension;
use PhpOffice\PhpSpreadsheet\Reader\Ods\AutoFilter;
use PhpOffice\PhpSpreadsheet\Reader\Ods\DefinedNames;
use PhpOffice\PhpSpreadsheet\Reader\Ods\FormulaTranslator;
use PhpOffice\PhpSpreadsheet\Reader\Ods\PageSettings;
use PhpOffice\PhpSpreadsheet\Reader\Ods\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;
use XMLReader;
use ZipArchive;

class Ods extends BaseReader
{
    const INITIAL_FILE = 'content.xml';

    /**
     * Create a new Ods Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $filename): bool
    {
        $mimeType = 'UNKNOWN';

        // Load file

        if (File::testFileNoThrow($filename, '')) {
            $zip = new ZipArchive();
            if ($zip->open($filename) === true) {
                // check if it is an OOXML archive
                $stat = $zip->statName('mimetype');
                if (!empty($stat) && ($stat['size'] <= 255)) {
                    $mimeType = $zip->getFromName($stat['name']);
                } elseif ($zip->statName('META-INF/manifest.xml')) {
                    $xml = simplexml_load_string(
                        $this->getSecurityScannerOrThrow()
                            ->scan(
                                $zip->getFromName(
                                    'META-INF/manifest.xml'
                                )
                            )
                    );
                    if ($xml !== false) {
                        $namespacesContent = $xml->getNamespaces(true);
                        if (isset($namespacesContent['manifest'])) {
                            $manifest = $xml->children($namespacesContent['manifest']);
                            foreach ($manifest as $manifestDataSet) {
                                $manifestAttributes = $manifestDataSet->attributes($namespacesContent['manifest']);
                                if ($manifestAttributes && $manifestAttributes->{'full-path'} == '/') {
                                    $mimeType = (string) $manifestAttributes->{'media-type'};

                                    break;
                                }
                            }
                        }
                    }
                }

                $zip->close();
            }
        }

        return $mimeType === 'application/vnd.oasis.opendocument.spreadsheet';
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PhpSpreadsheet object.
     *
     * @return string[]
     */
    public function listWorksheetNames(string $filename): array
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $worksheetNames = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->getSecurityScannerOrThrow()
                ->scanFile(
                    'zip://' . realpath($filename) . '#' . self::INITIAL_FILE
                )
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
                $xmlName = $xml->name;
                if ($xmlName == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    // Loop through each table:table node reading the table:name attribute for each worksheet name
                    do {
                        $worksheetName = $xml->getAttribute('table:name');
                        if (!empty($worksheetName)) {
                            $worksheetNames[] = $worksheetName;
                        }
                        $xml->next();
                    } while ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
                }
            }
        }

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     */
    public function listWorksheetInfo(string $filename): array
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $worksheetInfo = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->getSecurityScannerOrThrow()
                ->scanFile(
                    'zip://' . realpath($filename) . '#' . self::INITIAL_FILE
                )
        );
        $xml->setParserProperty(2, true);

        // Step into the first level of content of the XML
        $xml->read();
        $tableVisibility = [];
        $lastTableStyle = '';

        while ($xml->read()) {
            if ($xml->name === 'style:style') {
                $styleType = $xml->getAttribute('style:family');
                if ($styleType === 'table') {
                    $lastTableStyle = $xml->getAttribute('style:name');
                }
            } elseif ($xml->name === 'style:table-properties') {
                $visibility = $xml->getAttribute('table:display');
                $tableVisibility[$lastTableStyle] = ($visibility === 'false') ? Worksheet::SHEETSTATE_HIDDEN : Worksheet::SHEETSTATE_VISIBLE;
            } elseif ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                $worksheetNames[] = $xml->getAttribute('table:name');

                $styleName = $xml->getAttribute('table:style-name') ?? '';
                $visibility = $tableVisibility[$styleName] ?? '';
                $tmpInfo = [
                    'worksheetName' => $xml->getAttribute('table:name'),
                    'lastColumnLetter' => 'A',
                    'lastColumnIndex' => 0,
                    'totalRows' => 0,
                    'totalColumns' => 0,
                    'sheetState' => $visibility,
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
                            $doread = true;
                            if ($xml->name == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                if (!$xml->isEmptyElement) {
                                    ++$currCells;
                                    $xml->next();
                                    $doread = false;
                                }
                            } elseif ($xml->name == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                $mergeSize = $xml->getAttribute('table:number-columns-repeated');
                                $currCells += (int) $mergeSize;
                            }
                            if ($doread) {
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

        return $worksheetInfo;
    }

    /**
     * Loads PhpSpreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setValueBinder($this->valueBinder);
        $spreadsheet->removeSheetByIndex(0);

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     */
    public function loadIntoExisting(string $filename, Spreadsheet $spreadsheet): Spreadsheet
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $zip = new ZipArchive();
        $zip->open($filename);

        // Meta

        $xml = @simplexml_load_string(
            $this->getSecurityScannerOrThrow()
                ->scan($zip->getFromName('meta.xml'))
        );
        if ($xml === false) {
            throw new Exception('Unable to read data from {$pFilename}');
        }

        $namespacesMeta = $xml->getNamespaces(true);

        (new DocumentProperties($spreadsheet))->load($xml, $namespacesMeta);

        // Styles

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->getSecurityScannerOrThrow()
                ->scan($zip->getFromName('styles.xml'))
        );

        $pageSettings = new PageSettings($dom);

        // Main Content

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->getSecurityScannerOrThrow()
                ->scan($zip->getFromName(self::INITIAL_FILE))
        );

        $officeNs = (string) $dom->lookupNamespaceUri('office');
        $tableNs = (string) $dom->lookupNamespaceUri('table');
        $textNs = (string) $dom->lookupNamespaceUri('text');
        $xlinkNs = (string) $dom->lookupNamespaceUri('xlink');
        $styleNs = (string) $dom->lookupNamespaceUri('style');

        $pageSettings->readStyleCrossReferences($dom);

        $autoFilterReader = new AutoFilter($spreadsheet, $tableNs);
        $definedNameReader = new DefinedNames($spreadsheet, $tableNs);
        $columnWidths = [];
        $automaticStyle0 = $dom->getElementsByTagNameNS($officeNs, 'automatic-styles')->item(0);
        $automaticStyles = ($automaticStyle0 === null) ? [] : $automaticStyle0->getElementsByTagNameNS($styleNs, 'style');
        foreach ($automaticStyles as $automaticStyle) {
            $styleName = $automaticStyle->getAttributeNS($styleNs, 'name');
            $styleFamily = $automaticStyle->getAttributeNS($styleNs, 'family');
            if ($styleFamily === 'table-column') {
                $tcprops = $automaticStyle->getElementsByTagNameNS($styleNs, 'table-column-properties');
                $tcprop = $tcprops->item(0);
                if ($tcprop !== null) {
                    $columnWidth = $tcprop->getAttributeNs($styleNs, 'column-width');
                    $columnWidths[$styleName] = $columnWidth;
                }
            }
        }

        // Content
        $item0 = $dom->getElementsByTagNameNS($officeNs, 'body')->item(0);
        $spreadsheets = ($item0 === null) ? [] : $item0->getElementsByTagNameNS($officeNs, 'spreadsheet');

        foreach ($spreadsheets as $workbookData) {
            /** @var DOMElement $workbookData */
            $tables = $workbookData->getElementsByTagNameNS($tableNs, 'table');

            $worksheetID = 0;
            foreach ($tables as $worksheetDataSet) {
                /** @var DOMElement $worksheetDataSet */
                $worksheetName = $worksheetDataSet->getAttributeNS($tableNs, 'name');

                // Check loadSheetsOnly
                if (
                    $this->loadSheetsOnly !== null
                    && $worksheetName
                    && !in_array($worksheetName, $this->loadSheetsOnly)
                ) {
                    continue;
                }

                $worksheetStyleName = $worksheetDataSet->getAttributeNS($tableNs, 'style-name');

                // Create sheet
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($worksheetID);

                if ($worksheetName || is_numeric($worksheetName)) {
                    // Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                    // formula cells... during the load, all formulae should be correct, and we're simply
                    // bringing the worksheet name in line with the formula, not the reverse
                    $spreadsheet->getActiveSheet()->setTitle((string) $worksheetName, false, false);
                }

                // Go through every child of table element
                $rowID = 1;
                $tableColumnIndex = 1;
                foreach ($worksheetDataSet->childNodes as $childNode) {
                    /** @var DOMElement $childNode */

                    // Filter elements which are not under the "table" ns
                    if ($childNode->namespaceURI != $tableNs) {
                        continue;
                    }

                    $key = $childNode->nodeName;

                    // Remove ns from node name
                    if (str_contains($key, ':')) {
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
                        case 'table-column':
                            if ($childNode->hasAttributeNS($tableNs, 'number-columns-repeated')) {
                                $rowRepeats = (int) $childNode->getAttributeNS($tableNs, 'number-columns-repeated');
                            } else {
                                $rowRepeats = 1;
                            }
                            $tableStyleName = $childNode->getAttributeNS($tableNs, 'style-name');
                            if (isset($columnWidths[$tableStyleName])) {
                                $columnWidth = new HelperDimension($columnWidths[$tableStyleName]);
                                $tableColumnString = Coordinate::stringFromColumnIndex($tableColumnIndex);
                                for ($rowRepeats2 = $rowRepeats; $rowRepeats2 > 0; --$rowRepeats2) {
                                    $spreadsheet->getActiveSheet()
                                        ->getColumnDimension($tableColumnString)
                                        ->setWidth($columnWidth->toUnit('cm'), 'cm');
                                    ++$tableColumnString;
                                }
                            }
                            $tableColumnIndex += $rowRepeats;

                            break;
                        case 'table-row':
                            if ($childNode->hasAttributeNS($tableNs, 'number-rows-repeated')) {
                                $rowRepeats = (int) $childNode->getAttributeNS($tableNs, 'number-rows-repeated');
                            } else {
                                $rowRepeats = 1;
                            }

                            $columnID = 'A';
                            /** @var DOMElement|DOMText $cellData */
                            foreach ($childNode->childNodes as $cellData) {
                                if ($cellData instanceof DOMText) {
                                    continue; // should just be whitespace
                                }
                                if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                    if ($cellData->hasAttributeNS($tableNs, 'number-columns-repeated')) {
                                        $colRepeats = (int) $cellData->getAttributeNS($tableNs, 'number-columns-repeated');
                                    } else {
                                        $colRepeats = 1;
                                    }

                                    for ($i = 0; $i < $colRepeats; ++$i) {
                                        ++$columnID;
                                    }

                                    continue;
                                }

                                // Initialize variables
                                $formatting = $hyperlink = null;
                                $hasCalculatedValue = false;
                                $cellDataFormula = '';
                                $cellDataType = '';
                                $cellDataRef = '';

                                if ($cellData->hasAttributeNS($tableNs, 'formula')) {
                                    $cellDataFormula = $cellData->getAttributeNS($tableNs, 'formula');
                                    $hasCalculatedValue = true;
                                }
                                if ($cellData->hasAttributeNS($tableNs, 'number-matrix-columns-spanned')) {
                                    if ($cellData->hasAttributeNS($tableNs, 'number-matrix-rows-spanned')) {
                                        $cellDataType = 'array';
                                        $arrayRow = (int) $cellData->getAttributeNS($tableNs, 'number-matrix-rows-spanned');
                                        $arrayCol = (int) $cellData->getAttributeNS($tableNs, 'number-matrix-columns-spanned');
                                        $lastRow = $rowID + $arrayRow - 1;
                                        $lastCol = $columnID;
                                        while ($arrayCol > 1) {
                                            ++$lastCol;
                                            --$arrayCol;
                                        }
                                        $cellDataRef = "$columnID$rowID:$lastCol$lastRow";
                                    }
                                }

                                // Annotations
                                $annotation = $cellData->getElementsByTagNameNS($officeNs, 'annotation');

                                if ($annotation->length > 0 && $annotation->item(0) !== null) {
                                    $textNode = $annotation->item(0)->getElementsByTagNameNS($textNs, 'p');
                                    $textNodeLength = $textNode->length;
                                    $newLineOwed = false;
                                    for ($textNodeIndex = 0; $textNodeIndex < $textNodeLength; ++$textNodeIndex) {
                                        $textNodeItem = $textNode->item($textNodeIndex);
                                        if ($textNodeItem !== null) {
                                            $text = $this->scanElementForText($textNodeItem);
                                            if ($newLineOwed) {
                                                $spreadsheet->getActiveSheet()
                                                    ->getComment($columnID . $rowID)
                                                    ->getText()
                                                    ->createText("\n");
                                            }
                                            $newLineOwed = true;

                                            $spreadsheet->getActiveSheet()
                                                ->getComment($columnID . $rowID)
                                                ->getText()
                                                ->createText($this->parseRichText($text));
                                        }
                                    }
                                }

                                // Content

                                /** @var DOMElement[] $paragraphs */
                                $paragraphs = [];

                                foreach ($cellData->childNodes as $item) {
                                    /** @var DOMElement $item */

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
                                    $allCellDataText = implode("\n", $dataArray);

                                    $type = $cellData->getAttributeNS($officeNs, 'value-type');

                                    switch ($type) {
                                        case 'string':
                                            $type = DataType::TYPE_STRING;
                                            $dataValue = $allCellDataText;

                                            foreach ($paragraphs as $paragraph) {
                                                $link = $paragraph->getElementsByTagNameNS($textNs, 'a');
                                                if ($link->length > 0 && $link->item(0) !== null) {
                                                    $hyperlink = $link->item(0)->getAttributeNS($xlinkNs, 'href');
                                                }
                                            }

                                            break;
                                        case 'boolean':
                                            $type = DataType::TYPE_BOOL;
                                            $dataValue = ($cellData->getAttributeNS($officeNs, 'boolean-value') === 'true') ? true : false;

                                            break;
                                        case 'percentage':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            // percentage should always be float
                                            //if (floor($dataValue) == $dataValue) {
                                            //    $dataValue = (int) $dataValue;
                                            //}
                                            $formatting = NumberFormat::FORMAT_PERCENTAGE_00;

                                            break;
                                        case 'currency':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = NumberFormat::FORMAT_CURRENCY_USD_INTEGER;

                                            break;
                                        case 'float':
                                            $type = DataType::TYPE_NUMERIC;
                                            $dataValue = (float) $cellData->getAttributeNS($officeNs, 'value');

                                            if (floor($dataValue) == $dataValue) {
                                                if ($dataValue == (int) $dataValue) {
                                                    $dataValue = (int) $dataValue;
                                                }
                                            }

                                            break;
                                        case 'date':
                                            $type = DataType::TYPE_NUMERIC;
                                            $value = $cellData->getAttributeNS($officeNs, 'date-value');
                                            $dataValue = Date::convertIsoDate($value);

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
                                                    '01-01-1970 ' . implode(':', sscanf($timeValue, 'PT%dH%dM%dS') ?? [])
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
                                    $cellDataFormula = FormulaTranslator::convertToExcelFormulaValue($cellDataFormula);
                                }

                                if ($cellData->hasAttributeNS($tableNs, 'number-columns-repeated')) {
                                    $colRepeats = (int) $cellData->getAttributeNS($tableNs, 'number-columns-repeated');
                                } else {
                                    $colRepeats = 1;
                                }

                                if ($type !== null) { // @phpstan-ignore-line
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
                                                    if ($cellDataType === 'array') {
                                                        $cell->setFormulaAttributes(['t' => 'array', 'ref' => $cellDataRef]);
                                                    }
                                                } else {
                                                    $cell->setValueExplicit($dataValue, $type);
                                                }

                                                if ($hasCalculatedValue) {
                                                    $cell->setCalculatedValue($dataValue, $type === DataType::TYPE_NUMERIC);
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
                                                    if ($hyperlink[0] === '#') {
                                                        $hyperlink = 'sheet://' . substr($hyperlink, 1);
                                                    }
                                                    $cell->getHyperlink()
                                                        ->setUrl($hyperlink);
                                                }
                                            }
                                        }
                                    }
                                }

                                // Merged cells
                                $this->processMergedCells($cellData, $tableNs, $type, $columnID, $rowID, $spreadsheet);

                                ++$columnID;
                            }
                            $rowID += $rowRepeats;

                            break;
                    }
                }
                $pageSettings->setVisibilityForWorksheet($spreadsheet->getActiveSheet(), $worksheetStyleName);
                $pageSettings->setPrintSettingsForWorksheet($spreadsheet->getActiveSheet(), $worksheetStyleName);
                ++$worksheetID;
            }

            $autoFilterReader->read($workbookData);
            $definedNameReader->read($workbookData);
        }
        $spreadsheet->setActiveSheetIndex(0);

        if ($zip->locateName('settings.xml') !== false) {
            $this->processSettings($zip, $spreadsheet);
        }

        // Return
        return $spreadsheet;
    }

    private function processSettings(ZipArchive $zip, Spreadsheet $spreadsheet): void
    {
        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->getSecurityScannerOrThrow()
                ->scan($zip->getFromName('settings.xml'))
        );
        //$xlinkNs = $dom->lookupNamespaceUri('xlink');
        $configNs = (string) $dom->lookupNamespaceUri('config');
        //$oooNs = $dom->lookupNamespaceUri('ooo');
        $officeNs = (string) $dom->lookupNamespaceUri('office');
        $settings = $dom->getElementsByTagNameNS($officeNs, 'settings')
            ->item(0);
        if ($settings !== null) {
            $this->lookForActiveSheet($settings, $spreadsheet, $configNs);
            $this->lookForSelectedCells($settings, $spreadsheet, $configNs);
        }
    }

    private function lookForActiveSheet(DOMElement $settings, Spreadsheet $spreadsheet, string $configNs): void
    {
        /** @var DOMElement $t */
        foreach ($settings->getElementsByTagNameNS($configNs, 'config-item') as $t) {
            if ($t->getAttributeNs($configNs, 'name') === 'ActiveTable') {
                try {
                    $spreadsheet->setActiveSheetIndexByName($t->nodeValue ?? '');
                } catch (Throwable) {
                    // do nothing
                }

                break;
            }
        }
    }

    private function lookForSelectedCells(DOMElement $settings, Spreadsheet $spreadsheet, string $configNs): void
    {
        /** @var DOMElement $t */
        foreach ($settings->getElementsByTagNameNS($configNs, 'config-item-map-named') as $t) {
            if ($t->getAttributeNs($configNs, 'name') === 'Tables') {
                foreach ($t->getElementsByTagNameNS($configNs, 'config-item-map-entry') as $ws) {
                    $setRow = $setCol = '';
                    $wsname = $ws->getAttributeNs($configNs, 'name');
                    foreach ($ws->getElementsByTagNameNS($configNs, 'config-item') as $configItem) {
                        $attrName = $configItem->getAttributeNs($configNs, 'name');
                        if ($attrName === 'CursorPositionX') {
                            $setCol = $configItem->nodeValue;
                        }
                        if ($attrName === 'CursorPositionY') {
                            $setRow = $configItem->nodeValue;
                        }
                    }
                    $this->setSelected($spreadsheet, $wsname, "$setCol", "$setRow");
                }

                break;
            }
        }
    }

    private function setSelected(Spreadsheet $spreadsheet, string $wsname, string $setCol, string $setRow): void
    {
        if (is_numeric($setCol) && is_numeric($setRow)) {
            $sheet = $spreadsheet->getSheetByName($wsname);
            if ($sheet !== null) {
                $sheet->setSelectedCells([(int) $setCol + 1, (int) $setRow + 1]);
            }
        }
    }

    /**
     * Recursively scan element.
     */
    protected function scanElementForText(DOMNode $element): string
    {
        $str = '';
        foreach ($element->childNodes as $child) {
            /** @var DOMNode $child */
            if ($child->nodeType == XML_TEXT_NODE) {
                $str .= $child->nodeValue;
            } elseif ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'text:line-break') {
                $str .= "\n";
            } elseif ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'text:s') {
                // It's a space

                // Multiple spaces?
                $attributes = $child->attributes;
                /** @var ?DOMAttr $cAttr */
                $cAttr = ($attributes === null) ? null : $attributes->getNamedItem('c');
                $multiplier = self::getMultiplier($cAttr);
                $str .= str_repeat(' ', $multiplier);
            }

            if ($child->hasChildNodes()) {
                $str .= $this->scanElementForText($child);
            }
        }

        return $str;
    }

    private static function getMultiplier(?DOMAttr $cAttr): int
    {
        if ($cAttr) {
            $multiplier = (int) $cAttr->nodeValue;
        } else {
            $multiplier = 1;
        }

        return $multiplier;
    }

    private function parseRichText(string $is): RichText
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }

    private function processMergedCells(
        DOMElement $cellData,
        string $tableNs,
        string $type,
        string $columnID,
        int $rowID,
        Spreadsheet $spreadsheet
    ): void {
        if (
            $cellData->hasAttributeNS($tableNs, 'number-columns-spanned')
            || $cellData->hasAttributeNS($tableNs, 'number-rows-spanned')
        ) {
            if (($type !== DataType::TYPE_NULL) || ($this->readDataOnly === false)) {
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
                $spreadsheet->getActiveSheet()->mergeCells($cellRange, Worksheet::MERGE_CELL_CONTENT_HIDE);
            }
        }
    }
}
