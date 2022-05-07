<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Ods\AutoFilter;
use PhpOffice\PhpSpreadsheet\Reader\Ods\DefinedNames;
use PhpOffice\PhpSpreadsheet\Reader\Ods\FormulaTranslator;
use PhpOffice\PhpSpreadsheet\Reader\Ods\PageSettings;
use PhpOffice\PhpSpreadsheet\Reader\Ods\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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
                if ($stat && ($stat['size'] <= 255)) {
                    $mimeType = $zip->getFromName($stat['name']);
                } elseif ($zip->statName('META-INF/manifest.xml')) {
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
                            if ($manifestAttributes && $manifestAttributes->{'full-path'} == '/') {
                                $mimeType = (string) $manifestAttributes->{'media-type'};

                                break;
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
     * @param string $filename
     *
     * @return string[]
     */
    public function listWorksheetNames($filename)
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $worksheetNames = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip://' . realpath($filename) . '#' . self::INITIAL_FILE),
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        // Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            // Quickly jump through to the office:body node
            while (self::getXmlName($xml) !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            // Now read each node until we find our first table:table node
            while ($xml->read()) {
                $xmlName = self::getXmlName($xml);
                if ($xmlName == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    // Loop through each table:table node reading the table:name attribute for each worksheet name
                    do {
                        $worksheetNames[] = $xml->getAttribute('table:name');
                        $xml->next();
                    } while (self::getXmlName($xml) == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
                }
            }
        }

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $filename
     *
     * @return array
     */
    public function listWorksheetInfo($filename)
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $worksheetInfo = [];

        $xml = new XMLReader();
        $xml->xml(
            $this->securityScanner->scanFile('zip://' . realpath($filename) . '#' . self::INITIAL_FILE),
            null,
            Settings::getLibXmlLoaderOptions()
        );
        $xml->setParserProperty(2, true);

        // Step into the first level of content of the XML
        $xml->read();
        while ($xml->read()) {
            // Quickly jump through to the office:body node
            while (self::getXmlName($xml) !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            // Now read each node until we find our first table:table node
            while ($xml->read()) {
                if (self::getXmlName($xml) == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
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
                        if (self::getXmlName($xml) == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
                            $rowspan = $xml->getAttribute('table:number-rows-repeated');
                            $rowspan = empty($rowspan) ? 1 : $rowspan;
                            $tmpInfo['totalRows'] += $rowspan;
                            $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                            $currCells = 0;
                            // Step into the row
                            $xml->read();
                            do {
                                $doread = true;
                                if (self::getXmlName($xml) == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    if (!$xml->isEmptyElement) {
                                        ++$currCells;
                                        $xml->next();
                                        $doread = false;
                                    }
                                } elseif (self::getXmlName($xml) == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    $mergeSize = $xml->getAttribute('table:number-columns-repeated');
                                    $currCells += (int) $mergeSize;
                                }
                                if ($doread) {
                                    $xml->read();
                                }
                            } while (self::getXmlName($xml) != 'table:table-row');
                        }
                    } while (self::getXmlName($xml) != 'table:table');

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
     * Counteract Phpstan caching.
     *
     * @phpstan-impure
     */
    private static function getXmlName(XMLReader $xml): string
    {
        return $xml->name;
    }

    /**
     * Loads PhpSpreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     *
     * @param string $filename
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($filename, Spreadsheet $spreadsheet)
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $zip = new ZipArchive();
        $zip->open($filename);

        // Meta

        $xml = @simplexml_load_string(
            $this->securityScanner->scan($zip->getFromName('meta.xml')),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
        if ($xml === false) {
            throw new Exception('Unable to read data from {$pFilename}');
        }

        $namespacesMeta = $xml->getNamespaces(true);

        (new DocumentProperties($spreadsheet))->load($xml, $namespacesMeta);

        // Styles

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->securityScanner->scan($zip->getFromName('styles.xml')),
            Settings::getLibXmlLoaderOptions()
        );

        $pageSettings = new PageSettings($dom);

        // Main Content

        $dom = new DOMDocument('1.01', 'UTF-8');
        $dom->loadXML(
            $this->securityScanner->scan($zip->getFromName(self::INITIAL_FILE)),
            Settings::getLibXmlLoaderOptions()
        );

        $officeNs = $dom->lookupNamespaceUri('office');
        $tableNs = $dom->lookupNamespaceUri('table');
        $textNs = $dom->lookupNamespaceUri('text');
        $xlinkNs = $dom->lookupNamespaceUri('xlink');

        $pageSettings->readStyleCrossReferences($dom);

        $autoFilterReader = new AutoFilter($spreadsheet, $tableNs);
        $definedNameReader = new DefinedNames($spreadsheet, $tableNs);

        // Content
        $spreadsheets = $dom->getElementsByTagNameNS($officeNs, 'body')
            ->item(0)
            ->getElementsByTagNameNS($officeNs, 'spreadsheet');

        foreach ($spreadsheets as $workbookData) {
            /** @var DOMElement $workbookData */
            $tables = $workbookData->getElementsByTagNameNS($tableNs, 'table');

            $worksheetID = 0;
            foreach ($tables as $worksheetDataSet) {
                /** @var DOMElement $worksheetDataSet */
                $worksheetName = $worksheetDataSet->getAttributeNS($tableNs, 'name');

                // Check loadSheetsOnly
                if (
                    isset($this->loadSheetsOnly)
                    && $worksheetName
                    && !in_array($worksheetName, $this->loadSheetsOnly)
                ) {
                    continue;
                }

                $worksheetStyleName = $worksheetDataSet->getAttributeNS($tableNs, 'style-name');

                // Create sheet
                if ($worksheetID > 0) {
                    $spreadsheet->createSheet(); // First sheet is added by default
                }
                $spreadsheet->setActiveSheetIndex($worksheetID);

                if ($worksheetName || is_numeric($worksheetName)) {
                    // Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                    // formula cells... during the load, all formulae should be correct, and we're simply
                    // bringing the worksheet name in line with the formula, not the reverse
                    $spreadsheet->getActiveSheet()->setTitle((string) $worksheetName, false, false);
                }

                // Go through every child of table element
                $rowID = 1;
                foreach ($worksheetDataSet->childNodes as $childNode) {
                    /** @var DOMElement $childNode */

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
                                $rowRepeats = (int) $childNode->getAttributeNS($tableNs, 'number-rows-repeated');
                            } else {
                                $rowRepeats = 1;
                            }

                            $columnID = 'A';
                            /** @var DOMElement $cellData */
                            foreach ($childNode->childNodes as $cellData) {
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
                                            $formatting = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;

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
                                $this->processMergedCells($cellData, $tableNs, $type, $columnID, $rowID, $spreadsheet);

                                ++$columnID;
                            }
                            $rowID += $rowRepeats;

                            break;
                    }
                }
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
            $this->securityScanner->scan($zip->getFromName('settings.xml')),
            Settings::getLibXmlLoaderOptions()
        );
        //$xlinkNs = $dom->lookupNamespaceUri('xlink');
        $configNs = $dom->lookupNamespaceUri('config');
        //$oooNs = $dom->lookupNamespaceUri('ooo');
        $officeNs = $dom->lookupNamespaceUri('office');
        $settings = $dom->getElementsByTagNameNS($officeNs, 'settings')
            ->item(0);
        $this->lookForActiveSheet($settings, $spreadsheet, $configNs);
        $this->lookForSelectedCells($settings, $spreadsheet, $configNs);
    }

    private function lookForActiveSheet(DOMElement $settings, Spreadsheet $spreadsheet, string $configNs): void
    {
        /** @var DOMElement $t */
        foreach ($settings->getElementsByTagNameNS($configNs, 'config-item') as $t) {
            if ($t->getAttributeNs($configNs, 'name') === 'ActiveTable') {
                try {
                    $spreadsheet->setActiveSheetIndexByName($t->nodeValue ?? '');
                } catch (Throwable $e) {
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
            try {
                $spreadsheet->getSheetByName($wsname)->setSelectedCellByColumnAndRow($setCol + 1, $setRow + 1);
            } catch (Throwable $e) {
                // do nothing
            }
        }
    }

    /**
     * Recursively scan element.
     *
     * @return string
     */
    protected function scanElementForText(DOMNode $element)
    {
        $str = '';
        foreach ($element->childNodes as $child) {
            /** @var DOMNode $child */
            if ($child->nodeType == XML_TEXT_NODE) {
                $str .= $child->nodeValue;
            } elseif ($child->nodeType == XML_ELEMENT_NODE && $child->nodeName == 'text:s') {
                // It's a space

                // Multiple spaces?
                /** @var DOMAttr $cAttr */
                $cAttr = $child->attributes->getNamedItem('c');
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
                $spreadsheet->getActiveSheet()->mergeCells($cellRange);
            }
        }
    }
}
