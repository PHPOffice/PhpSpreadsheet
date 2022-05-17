<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\AutoFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\ColumnAndRowAttributes;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\ConditionalStyles;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\DataValidations;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Hyperlinks;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\PageSetup;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Properties as PropertyReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViewOptions;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViews;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Styles;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\WorkbookView;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use Throwable;
use XMLReader;
use ZipArchive;

class Xlsx extends BaseReader
{
    const INITIAL_FILE = '_rels/.rels';

    /**
     * ReferenceHelper instance.
     *
     * @var ReferenceHelper
     */
    private $referenceHelper;

    /**
     * @var ZipArchive
     */
    private $zip;

    /** @var Styles */
    private $styleReader;

    /**
     * Create a new Xlsx Reader instance.
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
        if (!File::testFileNoThrow($filename, self::INITIAL_FILE)) {
            return false;
        }

        $result = false;
        $this->zip = $zip = new ZipArchive();

        if ($zip->open($filename) === true) {
            [$workbookBasename] = $this->getWorkbookBaseName();
            $result = !empty($workbookBasename);

            $zip->close();
        }

        return $result;
    }

    /**
     * @param mixed $value
     */
    public static function testSimpleXml($value): SimpleXMLElement
    {
        return ($value instanceof SimpleXMLElement) ? $value : new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    }

    public static function getAttributes(?SimpleXMLElement $value, string $ns = ''): SimpleXMLElement
    {
        return self::testSimpleXml($value === null ? $value : $value->attributes($ns));
    }

    // Phpstan thinks, correctly, that xpath can return false.
    // Scrutinizer thinks it can't.
    // Sigh.
    private static function xpathNoFalse(SimpleXmlElement $sxml, string $path): array
    {
        return self::falseToArray($sxml->xpath($path));
    }

    /**
     * @param mixed $value
     */
    public static function falseToArray($value): array
    {
        return is_array($value) ? $value : [];
    }

    private function loadZip(string $filename, string $ns = ''): SimpleXMLElement
    {
        $contents = $this->getFromZipArchive($this->zip, $filename);
        $rels = simplexml_load_string(
            $this->securityScanner->scan($contents),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions(),
            $ns
        );

        return self::testSimpleXml($rels);
    }

    // This function is just to identify cases where I'm not sure
    // why empty namespace is required.
    private function loadZipNonamespace(string $filename, string $ns): SimpleXMLElement
    {
        $contents = $this->getFromZipArchive($this->zip, $filename);
        $rels = simplexml_load_string(
            $this->securityScanner->scan($contents),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions(),
            ($ns === '' ? $ns : '')
        );

        return self::testSimpleXml($rels);
    }

    private const REL_TO_MAIN = [
        Namespaces::PURL_OFFICE_DOCUMENT => Namespaces::PURL_MAIN,
        Namespaces::THUMBNAIL => '',
    ];

    private const REL_TO_DRAWING = [
        Namespaces::PURL_RELATIONSHIPS => Namespaces::PURL_DRAWING,
    ];

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object.
     *
     * @param string $filename
     *
     * @return array
     */
    public function listWorksheetNames($filename)
    {
        File::assertFile($filename, self::INITIAL_FILE);

        $worksheetNames = [];

        $this->zip = $zip = new ZipArchive();
        $zip->open($filename);

        //    The files we're looking at here are small enough that simpleXML is more efficient than XMLReader
        $rels = $this->loadZip(self::INITIAL_FILE, Namespaces::RELATIONSHIPS);
        foreach ($rels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relType = (string) $rel['Type'];
            $mainNS = self::REL_TO_MAIN[$relType] ?? Namespaces::MAIN;
            if ($mainNS !== '') {
                $xmlWorkbook = $this->loadZip((string) $rel['Target'], $mainNS);

                if ($xmlWorkbook->sheets) {
                    foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                        // Check if sheet should be skipped
                        $worksheetNames[] = (string) self::getAttributes($eleSheet)['name'];
                    }
                }
            }
        }

        $zip->close();

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

        $this->zip = $zip = new ZipArchive();
        $zip->open($filename);

        $rels = $this->loadZip(self::INITIAL_FILE, Namespaces::RELATIONSHIPS);
        foreach ($rels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relType = (string) $rel['Type'];
            $mainNS = self::REL_TO_MAIN[$relType] ?? Namespaces::MAIN;
            if ($mainNS !== '') {
                $relTarget = (string) $rel['Target'];
                $dir = dirname($relTarget);
                $namespace = dirname($relType);
                $relsWorkbook = $this->loadZip("$dir/_rels/" . basename($relTarget) . '.rels', '');

                $worksheets = [];
                foreach ($relsWorkbook->Relationship as $elex) {
                    $ele = self::getAttributes($elex);
                    if (
                        ((string) $ele['Type'] === "$namespace/worksheet") ||
                        ((string) $ele['Type'] === "$namespace/chartsheet")
                    ) {
                        $worksheets[(string) $ele['Id']] = $ele['Target'];
                    }
                }

                $xmlWorkbook = $this->loadZip($relTarget, $mainNS);
                if ($xmlWorkbook->sheets) {
                    $dir = dirname($relTarget);
                    /** @var SimpleXMLElement $eleSheet */
                    foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                        $tmpInfo = [
                            'worksheetName' => (string) self::getAttributes($eleSheet)['name'],
                            'lastColumnLetter' => 'A',
                            'lastColumnIndex' => 0,
                            'totalRows' => 0,
                            'totalColumns' => 0,
                        ];

                        $fileWorksheet = (string) $worksheets[(string) self::getArrayItem(self::getAttributes($eleSheet, $namespace), 'id')];
                        $fileWorksheetPath = strpos($fileWorksheet, '/') === 0 ? substr($fileWorksheet, 1) : "$dir/$fileWorksheet";

                        $xml = new XMLReader();
                        $xml->xml(
                            $this->securityScanner->scanFile(
                                'zip://' . File::realpath($filename) . '#' . $fileWorksheetPath
                            ),
                            null,
                            Settings::getLibXmlLoaderOptions()
                        );
                        $xml->setParserProperty(2, true);

                        $currCells = 0;
                        while ($xml->read()) {
                            if ($xml->localName == 'row' && $xml->nodeType == XMLReader::ELEMENT && $xml->namespaceURI === $mainNS) {
                                $row = $xml->getAttribute('r');
                                $tmpInfo['totalRows'] = $row;
                                $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                                $currCells = 0;
                            } elseif ($xml->localName == 'c' && $xml->nodeType == XMLReader::ELEMENT && $xml->namespaceURI === $mainNS) {
                                $cell = $xml->getAttribute('r');
                                $currCells = $cell ? max($currCells, Coordinate::indexesFromString($cell)[0]) : ($currCells + 1);
                            }
                        }
                        $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                        $xml->close();

                        $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                        $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);

                        $worksheetInfo[] = $tmpInfo;
                    }
                }
            }
        }

        $zip->close();

        return $worksheetInfo;
    }

    private static function castToBoolean($c)
    {
        $value = isset($c->v) ? (string) $c->v : null;
        if ($value == '0') {
            return false;
        } elseif ($value == '1') {
            return true;
        }

        return (bool) $c->v;
    }

    private static function castToError($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private static function castToString($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private function castToFormula($c, $r, &$cellDataType, &$value, &$calculatedValue, &$sharedFormulas, $castBaseType): void
    {
        $attr = $c->f->attributes();
        $cellDataType = 'f';
        $value = "={$c->f}";
        $calculatedValue = self::$castBaseType($c);

        // Shared formula?
        if (isset($attr['t']) && strtolower((string) $attr['t']) == 'shared') {
            $instance = (string) $attr['si'];

            if (!isset($sharedFormulas[(string) $attr['si']])) {
                $sharedFormulas[$instance] = ['master' => $r, 'formula' => $value];
            } else {
                $master = Coordinate::indexesFromString($sharedFormulas[$instance]['master']);
                $current = Coordinate::indexesFromString($r);

                $difference = [0, 0];
                $difference[0] = $current[0] - $master[0];
                $difference[1] = $current[1] - $master[1];

                $value = $this->referenceHelper->updateFormulaReferences($sharedFormulas[$instance]['formula'], 'A1', $difference[0], $difference[1]);
            }
        }
    }

    /**
     * @param string $fileName
     */
    private function fileExistsInArchive(ZipArchive $archive, $fileName = ''): bool
    {
        // Root-relative paths
        if (strpos($fileName, '//') !== false) {
            $fileName = substr($fileName, strpos($fileName, '//') + 1);
        }
        $fileName = File::realpath($fileName);

        // Sadly, some 3rd party xlsx generators don't use consistent case for filenaming
        //    so we need to load case-insensitively from the zip file

        // Apache POI fixes
        $contents = $archive->locateName($fileName, ZipArchive::FL_NOCASE);
        if ($contents === false) {
            $contents = $archive->locateName(substr($fileName, 1), ZipArchive::FL_NOCASE);
        }

        return $contents !== false;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFromZipArchive(ZipArchive $archive, $fileName = '')
    {
        // Root-relative paths
        if (strpos($fileName, '//') !== false) {
            $fileName = substr($fileName, strpos($fileName, '//') + 1);
        }
        // Relative paths generated by dirname($filename) when $filename
        // has no path (i.e.files in root of the zip archive)
        $fileName = (string) preg_replace('/^\.\//', '', $fileName);
        $fileName = File::realpath($fileName);

        // Sadly, some 3rd party xlsx generators don't use consistent case for filenaming
        //    so we need to load case-insensitively from the zip file

        // Apache POI fixes
        $contents = $archive->getFromName($fileName, 0, ZipArchive::FL_NOCASE);
        if ($contents === false) {
            $contents = $archive->getFromName(substr($fileName, 1), 0, ZipArchive::FL_NOCASE);
        }

        return $contents;
    }

    /**
     * Loads Spreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        File::assertFile($filename, self::INITIAL_FILE);

        // Initialisations
        $excel = new Spreadsheet();
        $excel->removeSheetByIndex(0);
        $addingFirstCellStyleXf = true;
        $addingFirstCellXf = true;

        $unparsedLoadedData = [];

        $this->zip = $zip = new ZipArchive();
        $zip->open($filename);

        //    Read the theme first, because we need the colour scheme when reading the styles
        [$workbookBasename, $xmlNamespaceBase] = $this->getWorkbookBaseName();
        $wbRels = $this->loadZip("xl/_rels/${workbookBasename}.rels", Namespaces::RELATIONSHIPS);
        $theme = null;
        $this->styleReader = new Styles();
        foreach ($wbRels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relTarget = (string) $rel['Target'];
            switch ($rel['Type']) {
                case "$xmlNamespaceBase/theme":
                    $themeOrderArray = ['lt1', 'dk1', 'lt2', 'dk2'];
                    $themeOrderAdditional = count($themeOrderArray);
                    $drawingNS = self::REL_TO_DRAWING[$xmlNamespaceBase] ?? Namespaces::DRAWINGML;

                    $xmlTheme = $this->loadZip("xl/{$relTarget}", $drawingNS);
                    $xmlThemeName = self::getAttributes($xmlTheme);
                    $xmlTheme = $xmlTheme->children($drawingNS);
                    $themeName = (string) $xmlThemeName['name'];

                    $colourScheme = self::getAttributes($xmlTheme->themeElements->clrScheme);
                    $colourSchemeName = (string) $colourScheme['name'];
                    $colourScheme = $xmlTheme->themeElements->clrScheme->children($drawingNS);

                    $themeColours = [];
                    foreach ($colourScheme as $k => $xmlColour) {
                        $themePos = array_search($k, $themeOrderArray);
                        if ($themePos === false) {
                            $themePos = $themeOrderAdditional++;
                        }
                        if (isset($xmlColour->sysClr)) {
                            $xmlColourData = self::getAttributes($xmlColour->sysClr);
                            $themeColours[$themePos] = (string) $xmlColourData['lastClr'];
                        } elseif (isset($xmlColour->srgbClr)) {
                            $xmlColourData = self::getAttributes($xmlColour->srgbClr);
                            $themeColours[$themePos] = (string) $xmlColourData['val'];
                        }
                    }
                    $theme = new Theme($themeName, $colourSchemeName, $themeColours);
                    $this->styleReader->setTheme($theme);

                    break;
            }
        }

        $rels = $this->loadZip(self::INITIAL_FILE, Namespaces::RELATIONSHIPS);

        $propertyReader = new PropertyReader($this->securityScanner, $excel->getProperties());
        foreach ($rels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relTarget = (string) $rel['Target'];
            $relType = (string) $rel['Type'];
            $mainNS = self::REL_TO_MAIN[$relType] ?? Namespaces::MAIN;
            switch ($relType) {
                case Namespaces::CORE_PROPERTIES:
                    $propertyReader->readCoreProperties($this->getFromZipArchive($zip, $relTarget));

                    break;
                case "$xmlNamespaceBase/extended-properties":
                    $propertyReader->readExtendedProperties($this->getFromZipArchive($zip, $relTarget));

                    break;
                case "$xmlNamespaceBase/custom-properties":
                    $propertyReader->readCustomProperties($this->getFromZipArchive($zip, $relTarget));

                    break;
                //Ribbon
                case Namespaces::EXTENSIBILITY:
                    $customUI = $relTarget;
                    if ($customUI) {
                        $this->readRibbon($excel, $customUI, $zip);
                    }

                    break;
                case "$xmlNamespaceBase/officeDocument":
                    $dir = dirname($relTarget);

                    // Do not specify namespace in next stmt - do it in Xpath
                    $relsWorkbook = $this->loadZip("$dir/_rels/" . basename($relTarget) . '.rels', '');
                    $relsWorkbook->registerXPathNamespace('rel', Namespaces::RELATIONSHIPS);

                    $sharedStrings = [];
                    $relType = "rel:Relationship[@Type='"
                        //. Namespaces::SHARED_STRINGS
                        . "$xmlNamespaceBase/sharedStrings"
                        . "']";
                    $xpath = self::getArrayItem($relsWorkbook->xpath($relType));

                    if ($xpath) {
                        $xmlStrings = $this->loadZip("$dir/$xpath[Target]", $mainNS);
                        if (isset($xmlStrings->si)) {
                            foreach ($xmlStrings->si as $val) {
                                if (isset($val->t)) {
                                    $sharedStrings[] = StringHelper::controlCharacterOOXML2PHP((string) $val->t);
                                } elseif (isset($val->r)) {
                                    $sharedStrings[] = $this->parseRichText($val);
                                }
                            }
                        }
                    }

                    $worksheets = [];
                    $macros = $customUI = null;
                    foreach ($relsWorkbook->Relationship as $elex) {
                        $ele = self::getAttributes($elex);
                        switch ($ele['Type']) {
                            case Namespaces::WORKSHEET:
                            case Namespaces::PURL_WORKSHEET:
                                $worksheets[(string) $ele['Id']] = $ele['Target'];

                                break;
                            case Namespaces::CHARTSHEET:
                                if ($this->includeCharts === true) {
                                    $worksheets[(string) $ele['Id']] = $ele['Target'];
                                }

                                break;
                            // a vbaProject ? (: some macros)
                            case Namespaces::VBA:
                                $macros = $ele['Target'];

                                break;
                        }
                    }

                    if ($macros !== null) {
                        $macrosCode = $this->getFromZipArchive($zip, 'xl/vbaProject.bin'); //vbaProject.bin always in 'xl' dir and always named vbaProject.bin
                        if ($macrosCode !== false) {
                            $excel->setMacrosCode($macrosCode);
                            $excel->setHasMacros(true);
                            //short-circuit : not reading vbaProject.bin.rel to get Signature =>allways vbaProjectSignature.bin in 'xl' dir
                            $Certificate = $this->getFromZipArchive($zip, 'xl/vbaProjectSignature.bin');
                            if ($Certificate !== false) {
                                $excel->setMacrosCertificate($Certificate);
                            }
                        }
                    }

                    $relType = "rel:Relationship[@Type='"
                        . "$xmlNamespaceBase/styles"
                        . "']";
                    $xpath = self::getArrayItem(self::xpathNoFalse($relsWorkbook, $relType));

                    if ($xpath === null) {
                        $xmlStyles = self::testSimpleXml(null);
                    } else {
                        $xmlStyles = $this->loadZip("$dir/$xpath[Target]", $mainNS);
                    }

                    $palette = self::extractPalette($xmlStyles);
                    $this->styleReader->setWorkbookPalette($palette);
                    $fills = self::extractStyles($xmlStyles, 'fills', 'fill');
                    $fonts = self::extractStyles($xmlStyles, 'fonts', 'font');
                    $borders = self::extractStyles($xmlStyles, 'borders', 'border');
                    $xfTags = self::extractStyles($xmlStyles, 'cellXfs', 'xf');
                    $cellXfTags = self::extractStyles($xmlStyles, 'cellStyleXfs', 'xf');

                    $styles = [];
                    $cellStyles = [];
                    $numFmts = null;
                    if (/*$xmlStyles && */ $xmlStyles->numFmts[0]) {
                        $numFmts = $xmlStyles->numFmts[0];
                    }
                    if (isset($numFmts) && ($numFmts !== null)) {
                        $numFmts->registerXPathNamespace('sml', $mainNS);
                    }
                    $this->styleReader->setNamespace($mainNS);
                    if (!$this->readDataOnly/* && $xmlStyles*/) {
                        foreach ($xfTags as $xfTag) {
                            $xf = self::getAttributes($xfTag);
                            $numFmt = null;

                            if ($xf['numFmtId']) {
                                if (isset($numFmts)) {
                                    $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));

                                    if (isset($tmpNumFmt['formatCode'])) {
                                        $numFmt = (string) $tmpNumFmt['formatCode'];
                                    }
                                }

                                // We shouldn't override any of the built-in MS Excel values (values below id 164)
                                //  But there's a lot of naughty homebrew xlsx writers that do use "reserved" id values that aren't actually used
                                //  So we make allowance for them rather than lose formatting masks
                                if (
                                    $numFmt === null &&
                                    (int) $xf['numFmtId'] < 164 &&
                                    NumberFormat::builtInFormatCode((int) $xf['numFmtId']) !== ''
                                ) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }
                            $quotePrefix = (bool) ($xf['quotePrefix'] ?? false);

                            $style = (object) [
                                'numFmt' => $numFmt ?? NumberFormat::FORMAT_GENERAL,
                                'font' => $fonts[(int) ($xf['fontId'])],
                                'fill' => $fills[(int) ($xf['fillId'])],
                                'border' => $borders[(int) ($xf['borderId'])],
                                'alignment' => $xfTag->alignment,
                                'protection' => $xfTag->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $styles[] = $style;

                            // add style to cellXf collection
                            $objStyle = new Style();
                            $this->styleReader->readStyle($objStyle, $style);
                            if ($addingFirstCellXf) {
                                $excel->removeCellXfByIndex(0); // remove the default style
                                $addingFirstCellXf = false;
                            }
                            $excel->addCellXf($objStyle);
                        }

                        foreach ($cellXfTags as $xfTag) {
                            $xf = self::getAttributes($xfTag);
                            $numFmt = NumberFormat::FORMAT_GENERAL;
                            if ($numFmts && $xf['numFmtId']) {
                                $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
                                if (isset($tmpNumFmt['formatCode'])) {
                                    $numFmt = (string) $tmpNumFmt['formatCode'];
                                } elseif ((int) $xf['numFmtId'] < 165) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }

                            $quotePrefix = (bool) ($xf['quotePrefix'] ?? false);

                            $cellStyle = (object) [
                                'numFmt' => $numFmt,
                                'font' => $fonts[(int) ($xf['fontId'])],
                                'fill' => $fills[((int) $xf['fillId'])],
                                'border' => $borders[(int) ($xf['borderId'])],
                                'alignment' => $xfTag->alignment,
                                'protection' => $xfTag->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $cellStyles[] = $cellStyle;

                            // add style to cellStyleXf collection
                            $objStyle = new Style();
                            $this->styleReader->readStyle($objStyle, $cellStyle);
                            if ($addingFirstCellStyleXf) {
                                $excel->removeCellStyleXfByIndex(0); // remove the default style
                                $addingFirstCellStyleXf = false;
                            }
                            $excel->addCellStyleXf($objStyle);
                        }
                    }
                    $this->styleReader->setStyleXml($xmlStyles);
                    $this->styleReader->setNamespace($mainNS);
                    $this->styleReader->setStyleBaseData($theme, $styles, $cellStyles);
                    $dxfs = $this->styleReader->dxfs($this->readDataOnly);
                    $styles = $this->styleReader->styles();

                    $xmlWorkbook = $this->loadZipNoNamespace($relTarget, $mainNS);
                    $xmlWorkbookNS = $this->loadZip($relTarget, $mainNS);

                    // Set base date
                    if ($xmlWorkbookNS->workbookPr) {
                        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
                        $attrs1904 = self::getAttributes($xmlWorkbookNS->workbookPr);
                        if (isset($attrs1904['date1904'])) {
                            if (self::boolean((string) $attrs1904['date1904'])) {
                                Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
                            }
                        }
                    }

                    // Set protection
                    $this->readProtection($excel, $xmlWorkbook);

                    $sheetId = 0; // keep track of new sheet id in final workbook
                    $oldSheetId = -1; // keep track of old sheet id in final workbook
                    $countSkippedSheets = 0; // keep track of number of skipped sheets
                    $mapSheetId = []; // mapping of sheet ids from old to new

                    $charts = $chartDetails = [];

                    if ($xmlWorkbookNS->sheets) {
                        /** @var SimpleXMLElement $eleSheet */
                        foreach ($xmlWorkbookNS->sheets->sheet as $eleSheet) {
                            $eleSheetAttr = self::getAttributes($eleSheet);
                            ++$oldSheetId;

                            // Check if sheet should be skipped
                            if (is_array($this->loadSheetsOnly) && !in_array((string) $eleSheetAttr['name'], $this->loadSheetsOnly)) {
                                ++$countSkippedSheets;
                                $mapSheetId[$oldSheetId] = null;

                                continue;
                            }

                            $sheetReferenceId = (string) self::getArrayItem(self::getAttributes($eleSheet, $xmlNamespaceBase), 'id');
                            if (isset($worksheets[$sheetReferenceId]) === false) {
                                ++$countSkippedSheets;
                                $mapSheetId[$oldSheetId] = null;

                                continue;
                            }
                            // Map old sheet id in original workbook to new sheet id.
                            // They will differ if loadSheetsOnly() is being used
                            $mapSheetId[$oldSheetId] = $oldSheetId - $countSkippedSheets;

                            // Load sheet
                            $docSheet = $excel->createSheet();
                            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet
                            //        references in formula cells... during the load, all formulae should be correct,
                            //        and we're simply bringing the worksheet name in line with the formula, not the
                            //        reverse
                            $docSheet->setTitle((string) $eleSheetAttr['name'], false, false);

                            $fileWorksheet = (string) $worksheets[$sheetReferenceId];
                            $xmlSheet = $this->loadZipNoNamespace("$dir/$fileWorksheet", $mainNS);
                            $xmlSheetNS = $this->loadZip("$dir/$fileWorksheet", $mainNS);

                            $sharedFormulas = [];

                            if (isset($eleSheetAttr['state']) && (string) $eleSheetAttr['state'] != '') {
                                $docSheet->setSheetState((string) $eleSheetAttr['state']);
                            }
                            if ($xmlSheetNS) {
                                $xmlSheetMain = $xmlSheetNS->children($mainNS);
                                // Setting Conditional Styles adjusts selected cells, so we need to execute this
                                //    before reading the sheet view data to get the actual selected cells
                                if (!$this->readDataOnly && ($xmlSheet->conditionalFormatting)) {
                                    (new ConditionalStyles($docSheet, $xmlSheet, $dxfs))->load();
                                }
                                if (!$this->readDataOnly && $xmlSheet->extLst) {
                                    (new ConditionalStyles($docSheet, $xmlSheet, $dxfs))->loadFromExt($this->styleReader);
                                }
                                if (isset($xmlSheetMain->sheetViews, $xmlSheetMain->sheetViews->sheetView)) {
                                    $sheetViews = new SheetViews($xmlSheetMain->sheetViews->sheetView, $docSheet);
                                    $sheetViews->load();
                                }

                                $sheetViewOptions = new SheetViewOptions($docSheet, $xmlSheet);
                                $sheetViewOptions->load($this->getReadDataOnly(), $this->styleReader);

                                (new ColumnAndRowAttributes($docSheet, $xmlSheet))
                                    ->load($this->getReadFilter(), $this->getReadDataOnly());
                            }

                            if ($xmlSheetNS && $xmlSheetNS->sheetData && $xmlSheetNS->sheetData->row) {
                                $cIndex = 1; // Cell Start from 1
                                foreach ($xmlSheetNS->sheetData->row as $row) {
                                    $rowIndex = 1;
                                    foreach ($row->c as $c) {
                                        $cAttr = self::getAttributes($c);
                                        $r = (string) $cAttr['r'];
                                        if ($r == '') {
                                            $r = Coordinate::stringFromColumnIndex($rowIndex) . $cIndex;
                                        }
                                        $cellDataType = (string) $cAttr['t'];
                                        $value = null;
                                        $calculatedValue = null;

                                        // Read cell?
                                        if ($this->getReadFilter() !== null) {
                                            $coordinates = Coordinate::coordinateFromString($r);

                                            if (!$this->getReadFilter()->readCell($coordinates[0], (int) $coordinates[1], $docSheet->getTitle())) {
                                                if (isset($cAttr->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                }
                                                ++$rowIndex;

                                                continue;
                                            }
                                        }

                                        // Read cell!
                                        switch ($cellDataType) {
                                            case 's':
                                                if ((string) $c->v != '') {
                                                    $value = $sharedStrings[(int) ($c->v)];

                                                    if ($value instanceof RichText) {
                                                        $value = clone $value;
                                                    }
                                                } else {
                                                    $value = '';
                                                }

                                                break;
                                            case 'b':
                                                if (!isset($c->f)) {
                                                    if (isset($c->v)) {
                                                        $value = self::castToBoolean($c);
                                                    } else {
                                                        $value = null;
                                                        $cellDataType = DATATYPE::TYPE_NULL;
                                                    }
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToBoolean');
                                                    if (isset($c->f['t'])) {
                                                        $att = $c->f;
                                                        $docSheet->getCell($r)->setFormulaAttributes($att);
                                                    }
                                                }

                                                break;
                                            case 'inlineStr':
                                                if (isset($c->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                } else {
                                                    $value = $this->parseRichText($c->is);
                                                }

                                                break;
                                            case 'e':
                                                if (!isset($c->f)) {
                                                    $value = self::castToError($c);
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToError');
                                                }

                                                break;
                                            default:
                                                if (!isset($c->f)) {
                                                    $value = self::castToString($c);
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToString');
                                                    if (isset($c->f['t'])) {
                                                        $attributes = $c->f['t'];
                                                        $docSheet->getCell($r)->setFormulaAttributes(['t' => (string) $attributes]);
                                                    }
                                                }

                                                break;
                                        }

                                        // read empty cells or the cells are not empty
                                        if ($this->readEmptyCells || ($value !== null && $value !== '')) {
                                            // Rich text?
                                            if ($value instanceof RichText && $this->readDataOnly) {
                                                $value = $value->getPlainText();
                                            }

                                            $cell = $docSheet->getCell($r);
                                            // Assign value
                                            if ($cellDataType != '') {
                                                // it is possible, that datatype is numeric but with an empty string, which result in an error
                                                if ($cellDataType === DataType::TYPE_NUMERIC && ($value === '' || $value === null)) {
                                                    $cellDataType = DataType::TYPE_NULL;
                                                }
                                                if ($cellDataType !== DataType::TYPE_NULL) {
                                                    $cell->setValueExplicit($value, $cellDataType);
                                                }
                                            } else {
                                                $cell->setValue($value);
                                            }
                                            if ($calculatedValue !== null) {
                                                $cell->setCalculatedValue($calculatedValue);
                                            }

                                            // Style information?
                                            if ($cAttr['s'] && !$this->readDataOnly) {
                                                // no style index means 0, it seems
                                                $cell->setXfIndex(isset($styles[(int) ($cAttr['s'])]) ?
                                                    (int) ($cAttr['s']) : 0);
                                            }
                                        }
                                        ++$rowIndex;
                                    }
                                    ++$cIndex;
                                }
                            }

                            $aKeys = ['sheet', 'objects', 'scenarios', 'formatCells', 'formatColumns', 'formatRows', 'insertColumns', 'insertRows', 'insertHyperlinks', 'deleteColumns', 'deleteRows', 'selectLockedCells', 'sort', 'autoFilter', 'pivotTables', 'selectUnlockedCells'];
                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
                                foreach ($aKeys as $key) {
                                    $method = 'set' . ucfirst($key);
                                    $docSheet->getProtection()->$method(self::boolean((string) $xmlSheet->sheetProtection[$key]));
                                }
                            }

                            if ($xmlSheet) {
                                $this->readSheetProtection($docSheet, $xmlSheet);
                            }

                            if ($this->readDataOnly === false) {
                                $this->readAutoFilterTables($xmlSheet, $docSheet, $dir, $fileWorksheet, $zip);
                            }

                            if ($xmlSheet && $xmlSheet->mergeCells && $xmlSheet->mergeCells->mergeCell && !$this->readDataOnly) {
                                foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
                                    $mergeRef = (string) $mergeCell['ref'];
                                    if (strpos($mergeRef, ':') !== false) {
                                        $docSheet->mergeCells((string) $mergeCell['ref']);
                                    }
                                }
                            }

                            if ($xmlSheet && !$this->readDataOnly) {
                                $unparsedLoadedData = (new PageSetup($docSheet, $xmlSheet))->load($unparsedLoadedData);
                            }

                            if ($xmlSheet !== false && isset($xmlSheet->extLst, $xmlSheet->extLst->ext, $xmlSheet->extLst->ext['uri']) && ($xmlSheet->extLst->ext['uri'] == '{CCE6A557-97BC-4b89-ADB6-D9C93CAAB3DF}')) {
                                // Create dataValidations node if does not exists, maybe is better inside the foreach ?
                                if (!$xmlSheet->dataValidations) {
                                    $xmlSheet->addChild('dataValidations');
                                }

                                foreach ($xmlSheet->extLst->ext->children('x14', true)->dataValidations->dataValidation as $item) {
                                    $node = self::testSimpleXml($xmlSheet->dataValidations)->addChild('dataValidation');
                                    foreach ($item->attributes() ?? [] as $attr) {
                                        $node->addAttribute($attr->getName(), $attr);
                                    }
                                    $node->addAttribute('sqref', $item->children('xm', true)->sqref);
                                    $node->addChild('formula1', $item->formula1->children('xm', true)->f);
                                }
                            }

                            if ($xmlSheet && $xmlSheet->dataValidations && !$this->readDataOnly) {
                                (new DataValidations($docSheet, $xmlSheet))->load();
                            }

                            // unparsed sheet AlternateContent
                            if ($xmlSheet && !$this->readDataOnly) {
                                $mc = $xmlSheet->children(Namespaces::COMPATIBILITY);
                                if ($mc->AlternateContent) {
                                    foreach ($mc->AlternateContent as $alternateContent) {
                                        $alternateContent = self::testSimpleXml($alternateContent);
                                        $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['AlternateContents'][] = $alternateContent->asXML();
                                    }
                                }
                            }

                            // Add hyperlinks
                            if (!$this->readDataOnly) {
                                $hyperlinkReader = new Hyperlinks($docSheet);
                                // Locate hyperlink relations
                                $relationsFileName = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';
                                if ($zip->locateName($relationsFileName)) {
                                    $relsWorksheet = $this->loadZip($relationsFileName, Namespaces::RELATIONSHIPS);
                                    $hyperlinkReader->readHyperlinks($relsWorksheet);
                                }

                                // Loop through hyperlinks
                                if ($xmlSheetNS && $xmlSheetNS->children($mainNS)->hyperlinks) {
                                    $hyperlinkReader->setHyperlinks($xmlSheetNS->children($mainNS)->hyperlinks);
                                }
                            }

                            // Add comments
                            $comments = [];
                            $vmlComments = [];
                            if (!$this->readDataOnly) {
                                // Locate comment relations
                                $commentRelations = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';
                                if ($zip->locateName($commentRelations)) {
                                    $relsWorksheet = $this->loadZip($commentRelations, Namespaces::RELATIONSHIPS);
                                    foreach ($relsWorksheet->Relationship as $elex) {
                                        $ele = self::getAttributes($elex);
                                        if ($ele['Type'] == Namespaces::COMMENTS) {
                                            $comments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                        if ($ele['Type'] == Namespaces::VML) {
                                            $vmlComments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                    }
                                }

                                // Loop through comments
                                foreach ($comments as $relName => $relPath) {
                                    // Load comments file
                                    $relPath = File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);
                                    // okay to ignore namespace - using xpath
                                    $commentsFile = $this->loadZip($relPath, '');

                                    // Utility variables
                                    $authors = [];
                                    $commentsFile->registerXpathNamespace('com', $mainNS);
                                    $authorPath = self::xpathNoFalse($commentsFile, 'com:authors/com:author');
                                    foreach ($authorPath as $author) {
                                        $authors[] = (string) $author;
                                    }

                                    // Loop through contents
                                    $contentPath = self::xpathNoFalse($commentsFile, 'com:commentList/com:comment');
                                    foreach ($contentPath as $comment) {
                                        $commentx = $comment->attributes();
                                        $commentModel = $docSheet->getComment((string) $commentx['ref']);
                                        if (isset($commentx['authorId'])) {
                                            $commentModel->setAuthor($authors[(int) $commentx['authorId']]);
                                        }
                                        $commentModel->setText($this->parseRichText($comment->children($mainNS)->text));
                                    }
                                }

                                // later we will remove from it real vmlComments
                                $unparsedVmlDrawings = $vmlComments;

                                // Loop through VML comments
                                foreach ($vmlComments as $relName => $relPath) {
                                    // Load VML comments file
                                    $relPath = File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);

                                    try {
                                        // no namespace okay - processed with Xpath
                                        $vmlCommentsFile = $this->loadZip($relPath, '');
                                        $vmlCommentsFile->registerXPathNamespace('v', Namespaces::URN_VML);
                                    } catch (Throwable $ex) {
                                        //Ignore unparsable vmlDrawings. Later they will be moved from $unparsedVmlDrawings to $unparsedLoadedData
                                        continue;
                                    }

                                    // Locate VML drawings image relations
                                    $drowingImages = [];
                                    $VMLDrawingsRelations = dirname($relPath) . '/_rels/' . basename($relPath) . '.rels';
                                    if ($zip->locateName($VMLDrawingsRelations)) {
                                        $relsVMLDrawing = $this->loadZip($VMLDrawingsRelations, Namespaces::RELATIONSHIPS);
                                        foreach ($relsVMLDrawing->Relationship as $elex) {
                                            $ele = self::getAttributes($elex);
                                            if ($ele['Type'] == Namespaces::IMAGE) {
                                                $drowingImages[(string) $ele['Id']] = (string) $ele['Target'];
                                            }
                                        }
                                    }

                                    $shapes = self::xpathNoFalse($vmlCommentsFile, '//v:shape');
                                    foreach ($shapes as $shape) {
                                        $shape->registerXPathNamespace('v', Namespaces::URN_VML);

                                        if (isset($shape['style'])) {
                                            $style = (string) $shape['style'];
                                            $fillColor = strtoupper(substr((string) $shape['fillcolor'], 1));
                                            $column = null;
                                            $row = null;
                                            $fillImageRelId = null;
                                            $fillImageTitle = '';

                                            $clientData = $shape->xpath('.//x:ClientData');
                                            if (is_array($clientData) && !empty($clientData)) {
                                                $clientData = $clientData[0];

                                                if (isset($clientData['ObjectType']) && (string) $clientData['ObjectType'] == 'Note') {
                                                    $temp = $clientData->xpath('.//x:Row');
                                                    if (is_array($temp)) {
                                                        $row = $temp[0];
                                                    }

                                                    $temp = $clientData->xpath('.//x:Column');
                                                    if (is_array($temp)) {
                                                        $column = $temp[0];
                                                    }
                                                }
                                            }

                                            $fillImageRelNode = $shape->xpath('.//v:fill/@o:relid');
                                            if (is_array($fillImageRelNode) && !empty($fillImageRelNode)) {
                                                $fillImageRelNode = $fillImageRelNode[0];

                                                if (isset($fillImageRelNode['relid'])) {
                                                    $fillImageRelId = (string) $fillImageRelNode['relid'];
                                                }
                                            }

                                            $fillImageTitleNode = $shape->xpath('.//v:fill/@o:title');
                                            if (is_array($fillImageTitleNode) && !empty($fillImageTitleNode)) {
                                                $fillImageTitleNode = $fillImageTitleNode[0];

                                                if (isset($fillImageTitleNode['title'])) {
                                                    $fillImageTitle = (string) $fillImageTitleNode['title'];
                                                }
                                            }

                                            if (($column !== null) && ($row !== null)) {
                                                // Set comment properties
                                                $comment = $docSheet->getCommentByColumnAndRow($column + 1, $row + 1);
                                                $comment->getFillColor()->setRGB($fillColor);
                                                if (isset($drowingImages[$fillImageRelId])) {
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName($fillImageTitle);
                                                    $imagePath = str_replace('../', 'xl/', $drowingImages[$fillImageRelId]);
                                                    $objDrawing->setPath(
                                                        'zip://' . File::realpath($filename) . '#' . $imagePath,
                                                        true,
                                                        $zip
                                                    );
                                                    $comment->setBackgroundImage($objDrawing);
                                                }

                                                // Parse style
                                                $styleArray = explode(';', str_replace(' ', '', $style));
                                                foreach ($styleArray as $stylePair) {
                                                    $stylePair = explode(':', $stylePair);

                                                    if ($stylePair[0] == 'margin-left') {
                                                        $comment->setMarginLeft($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'margin-top') {
                                                        $comment->setMarginTop($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'width') {
                                                        $comment->setWidth($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'height') {
                                                        $comment->setHeight($stylePair[1]);
                                                    }
                                                    if ($stylePair[0] == 'visibility') {
                                                        $comment->setVisible($stylePair[1] == 'visible');
                                                    }
                                                }

                                                unset($unparsedVmlDrawings[$relName]);
                                            }
                                        }
                                    }
                                }

                                // unparsed vmlDrawing
                                if ($unparsedVmlDrawings) {
                                    foreach ($unparsedVmlDrawings as $rId => $relPath) {
                                        $rId = substr($rId, 3); // rIdXXX
                                        $unparsedVmlDrawing = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['vmlDrawings'];
                                        $unparsedVmlDrawing[$rId] = [];
                                        $unparsedVmlDrawing[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $relPath);
                                        $unparsedVmlDrawing[$rId]['relFilePath'] = $relPath;
                                        $unparsedVmlDrawing[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedVmlDrawing[$rId]['filePath']));
                                        unset($unparsedVmlDrawing);
                                    }
                                }

                                // Header/footer images
                                if ($xmlSheet && $xmlSheet->legacyDrawingHF && !$this->readDataOnly) {
                                    if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                        $relsWorksheet = $this->loadZipNoNamespace(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels', Namespaces::RELATIONSHIPS);
                                        $vmlRelationship = '';

                                        foreach ($relsWorksheet->Relationship as $ele) {
                                            if ($ele['Type'] == Namespaces::VML) {
                                                $vmlRelationship = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                            }
                                        }

                                        if ($vmlRelationship != '') {
                                            // Fetch linked images
                                            $relsVML = $this->loadZipNoNamespace(dirname($vmlRelationship) . '/_rels/' . basename($vmlRelationship) . '.rels', Namespaces::RELATIONSHIPS);
                                            $drawings = [];
                                            if (isset($relsVML->Relationship)) {
                                                foreach ($relsVML->Relationship as $ele) {
                                                    if ($ele['Type'] == Namespaces::IMAGE) {
                                                        $drawings[(string) $ele['Id']] = self::dirAdd($vmlRelationship, $ele['Target']);
                                                    }
                                                }
                                            }
                                            // Fetch VML document
                                            $vmlDrawing = $this->loadZipNoNamespace($vmlRelationship, '');
                                            $vmlDrawing->registerXPathNamespace('v', Namespaces::URN_VML);

                                            $hfImages = [];

                                            $shapes = self::xpathNoFalse($vmlDrawing, '//v:shape');
                                            foreach ($shapes as $idx => $shape) {
                                                $shape->registerXPathNamespace('v', Namespaces::URN_VML);
                                                $imageData = $shape->xpath('//v:imagedata');

                                                if (empty($imageData)) {
                                                    continue;
                                                }

                                                $imageData = $imageData[$idx];

                                                $imageData = self::getAttributes($imageData, Namespaces::URN_MSOFFICE);
                                                $style = self::toCSSArray((string) $shape['style']);

                                                $hfImages[(string) $shape['id']] = new HeaderFooterDrawing();
                                                if (isset($imageData['title'])) {
                                                    $hfImages[(string) $shape['id']]->setName((string) $imageData['title']);
                                                }

                                                $hfImages[(string) $shape['id']]->setPath('zip://' . File::realpath($filename) . '#' . $drawings[(string) $imageData['relid']], false);
                                                $hfImages[(string) $shape['id']]->setResizeProportional(false);
                                                $hfImages[(string) $shape['id']]->setWidth($style['width']);
                                                $hfImages[(string) $shape['id']]->setHeight($style['height']);
                                                if (isset($style['margin-left'])) {
                                                    $hfImages[(string) $shape['id']]->setOffsetX($style['margin-left']);
                                                }
                                                $hfImages[(string) $shape['id']]->setOffsetY($style['margin-top']);
                                                $hfImages[(string) $shape['id']]->setResizeProportional(true);
                                            }

                                            $docSheet->getHeaderFooter()->setImages($hfImages);
                                        }
                                    }
                                }
                            }

                            // TODO: Autoshapes from twoCellAnchors!
                            $drawingFilename = dirname("$dir/$fileWorksheet")
                                . '/_rels/'
                                . basename($fileWorksheet)
                                . '.rels';
                            if ($zip->locateName($drawingFilename)) {
                                $relsWorksheet = $this->loadZipNoNamespace($drawingFilename, Namespaces::RELATIONSHIPS);
                                $drawings = [];
                                foreach ($relsWorksheet->Relationship as $ele) {
                                    if ((string) $ele['Type'] === "$xmlNamespaceBase/drawing") {
                                        $drawings[(string) $ele['Id']] = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                    }
                                }

                                if ($xmlSheet->drawing && !$this->readDataOnly) {
                                    $unparsedDrawings = [];
                                    $fileDrawing = null;
                                    foreach ($xmlSheet->drawing as $drawing) {
                                        $drawingRelId = (string) self::getArrayItem(self::getAttributes($drawing, $xmlNamespaceBase), 'id');
                                        $fileDrawing = $drawings[$drawingRelId];
                                        $drawingFilename = dirname($fileDrawing) . '/_rels/' . basename($fileDrawing) . '.rels';
                                        $relsDrawing = $this->loadZipNoNamespace($drawingFilename, $xmlNamespaceBase);

                                        $images = [];
                                        $hyperlinks = [];
                                        if ($relsDrawing && $relsDrawing->Relationship) {
                                            foreach ($relsDrawing->Relationship as $ele) {
                                                $eleType = (string) $ele['Type'];
                                                if ($eleType === Namespaces::HYPERLINK) {
                                                    $hyperlinks[(string) $ele['Id']] = (string) $ele['Target'];
                                                }
                                                if ($eleType === "$xmlNamespaceBase/image") {
                                                    $images[(string) $ele['Id']] = self::dirAdd($fileDrawing, $ele['Target']);
                                                } elseif ($eleType === "$xmlNamespaceBase/chart") {
                                                    if ($this->includeCharts) {
                                                        $charts[self::dirAdd($fileDrawing, $ele['Target'])] = [
                                                            'id' => (string) $ele['Id'],
                                                            'sheet' => $docSheet->getTitle(),
                                                        ];
                                                    }
                                                }
                                            }
                                        }

                                        $xmlDrawing = $this->loadZipNoNamespace($fileDrawing, '');
                                        $xmlDrawingChildren = $xmlDrawing->children(Namespaces::SPREADSHEET_DRAWING);

                                        if ($xmlDrawingChildren->oneCellAnchor) {
                                            foreach ($xmlDrawingChildren->oneCellAnchor as $oneCellAnchor) {
                                                if ($oneCellAnchor->pic->blipFill) {
                                                    /** @var SimpleXMLElement $blip */
                                                    $blip = $oneCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->blip;
                                                    /** @var SimpleXMLElement $xfrm */
                                                    $xfrm = $oneCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->xfrm;
                                                    /** @var SimpleXMLElement $outerShdw */
                                                    $outerShdw = $oneCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->effectLst->outerShdw;
                                                    /** @var SimpleXMLElement $hlinkClick */
                                                    $hlinkClick = $oneCellAnchor->pic->nvPicPr->cNvPr->children(Namespaces::DRAWINGML)->hlinkClick;

                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName((string) self::getArrayItem(self::getAttributes($oneCellAnchor->pic->nvPicPr->cNvPr), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem(self::getAttributes($oneCellAnchor->pic->nvPicPr->cNvPr), 'descr'));
                                                    $embedImageKey = (string) self::getArrayItem(
                                                        self::getAttributes($blip, $xmlNamespaceBase),
                                                        'embed'
                                                    );
                                                    if (isset($images[$embedImageKey])) {
                                                        $objDrawing->setPath(
                                                            'zip://' . File::realpath($filename) . '#' .
                                                            $images[$embedImageKey],
                                                            false
                                                        );
                                                    } else {
                                                        $linkImageKey = (string) self::getArrayItem(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'link'
                                                        );
                                                        if (isset($images[$linkImageKey])) {
                                                            $url = str_replace('xl/drawings/', '', $images[$linkImageKey]);
                                                            $objDrawing->setPath($url);
                                                        }
                                                    }
                                                    $objDrawing->setCoordinates(Coordinate::stringFromColumnIndex(((int) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1));

                                                    $objDrawing->setOffsetX((int) Drawing::EMUToPixels($oneCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);
                                                    $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($oneCellAnchor->ext), 'cx')));
                                                    $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($oneCellAnchor->ext), 'cy')));
                                                    if ($xfrm) {
                                                        $objDrawing->setRotation((int) Drawing::angleToDegrees(self::getArrayItem(self::getAttributes($xfrm), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($outerShdw), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($outerShdw), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItem(self::getAttributes($outerShdw), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem(self::getAttributes($outerShdw), 'algn'));
                                                        $clr = $outerShdw->srgbClr ?? $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItem(self::getAttributes($clr), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem(self::getAttributes($clr->alpha), 'val') / 1000);
                                                    }

                                                    $this->readHyperLinkDrawing($objDrawing, $oneCellAnchor, $hyperlinks);

                                                    $objDrawing->setWorksheet($docSheet);
                                                } elseif ($this->includeCharts && $oneCellAnchor->graphicFrame) {
                                                    // Exported XLSX from Google Sheets positions charts with a oneCellAnchor
                                                    $coordinates = Coordinate::stringFromColumnIndex(((int) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1);
                                                    $offsetX = Drawing::EMUToPixels($oneCellAnchor->from->colOff);
                                                    $offsetY = Drawing::EMUToPixels($oneCellAnchor->from->rowOff);
                                                    $width = Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($oneCellAnchor->ext), 'cx'));
                                                    $height = Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($oneCellAnchor->ext), 'cy'));

                                                    $graphic = $oneCellAnchor->graphicFrame->children(Namespaces::DRAWINGML)->graphic;
                                                    /** @var SimpleXMLElement $chartRef */
                                                    $chartRef = $graphic->graphicData->children(Namespaces::CHART)->chart;
                                                    $thisChart = (string) self::getAttributes($chartRef, $xmlNamespaceBase);

                                                    $chartDetails[$docSheet->getTitle() . '!' . $thisChart] = [
                                                        'fromCoordinate' => $coordinates,
                                                        'fromOffsetX' => $offsetX,
                                                        'fromOffsetY' => $offsetY,
                                                        'width' => $width,
                                                        'height' => $height,
                                                        'worksheetTitle' => $docSheet->getTitle(),
                                                    ];
                                                }
                                            }
                                        }
                                        if ($xmlDrawingChildren->twoCellAnchor) {
                                            foreach ($xmlDrawingChildren->twoCellAnchor as $twoCellAnchor) {
                                                if ($twoCellAnchor->pic->blipFill) {
                                                    $blip = $twoCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->blip;
                                                    $xfrm = $twoCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->xfrm;
                                                    $outerShdw = $twoCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->effectLst->outerShdw;
                                                    $hlinkClick = $twoCellAnchor->pic->nvPicPr->cNvPr->children(Namespaces::DRAWINGML)->hlinkClick;
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    /** @scrutinizer ignore-call */
                                                    $editAs = $twoCellAnchor->attributes();
                                                    if (isset($editAs, $editAs['editAs'])) {
                                                        $objDrawing->setEditAs($editAs['editAs']);
                                                    }
                                                    $objDrawing->setName((string) self::getArrayItem(self::getAttributes($twoCellAnchor->pic->nvPicPr->cNvPr), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem(self::getAttributes($twoCellAnchor->pic->nvPicPr->cNvPr), 'descr'));
                                                    $embedImageKey = (string) self::getArrayItem(
                                                        self::getAttributes($blip, $xmlNamespaceBase),
                                                        'embed'
                                                    );
                                                    if (isset($images[$embedImageKey])) {
                                                        $objDrawing->setPath(
                                                            'zip://' . File::realpath($filename) . '#' .
                                                            $images[$embedImageKey],
                                                            false
                                                        );
                                                    } else {
                                                        $linkImageKey = (string) self::getArrayItem(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'link'
                                                        );
                                                        if (isset($images[$linkImageKey])) {
                                                            $url = str_replace('xl/drawings/', '', $images[$linkImageKey]);
                                                            $objDrawing->setPath($url);
                                                        }
                                                    }
                                                    $objDrawing->setCoordinates(Coordinate::stringFromColumnIndex(((int) $twoCellAnchor->from->col) + 1) . ($twoCellAnchor->from->row + 1));

                                                    $objDrawing->setOffsetX(Drawing::EMUToPixels($twoCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(Drawing::EMUToPixels($twoCellAnchor->from->rowOff));

                                                    $objDrawing->setCoordinates2(Coordinate::stringFromColumnIndex(((int) $twoCellAnchor->to->col) + 1) . ($twoCellAnchor->to->row + 1));

                                                    $objDrawing->setOffsetX2(Drawing::EMUToPixels($twoCellAnchor->to->colOff));
                                                    $objDrawing->setOffsetY2(Drawing::EMUToPixels($twoCellAnchor->to->rowOff));

                                                    $objDrawing->setResizeProportional(false);

                                                    if ($xfrm) {
                                                        $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($xfrm->ext), 'cx')));
                                                        $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($xfrm->ext), 'cy')));
                                                        $objDrawing->setRotation(Drawing::angleToDegrees(self::getArrayItem(self::getAttributes($xfrm), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($outerShdw), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItem(self::getAttributes($outerShdw), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItem(self::getAttributes($outerShdw), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem(self::getAttributes($outerShdw), 'algn'));
                                                        $clr = $outerShdw->srgbClr ?? $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItem(self::getAttributes($clr), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem(self::getAttributes($clr->alpha), 'val') / 1000);
                                                    }

                                                    $this->readHyperLinkDrawing($objDrawing, $twoCellAnchor, $hyperlinks);

                                                    $objDrawing->setWorksheet($docSheet);
                                                } elseif (($this->includeCharts) && ($twoCellAnchor->graphicFrame)) {
                                                    $fromCoordinate = Coordinate::stringFromColumnIndex(((int) $twoCellAnchor->from->col) + 1) . ($twoCellAnchor->from->row + 1);
                                                    $fromOffsetX = Drawing::EMUToPixels($twoCellAnchor->from->colOff);
                                                    $fromOffsetY = Drawing::EMUToPixels($twoCellAnchor->from->rowOff);
                                                    $toCoordinate = Coordinate::stringFromColumnIndex(((int) $twoCellAnchor->to->col) + 1) . ($twoCellAnchor->to->row + 1);
                                                    $toOffsetX = Drawing::EMUToPixels($twoCellAnchor->to->colOff);
                                                    $toOffsetY = Drawing::EMUToPixels($twoCellAnchor->to->rowOff);
                                                    $graphic = $twoCellAnchor->graphicFrame->children(Namespaces::DRAWINGML)->graphic;
                                                    /** @var SimpleXMLElement $chartRef */
                                                    $chartRef = $graphic->graphicData->children(Namespaces::CHART)->chart;
                                                    $thisChart = (string) self::getAttributes($chartRef, $xmlNamespaceBase);

                                                    $chartDetails[$docSheet->getTitle() . '!' . $thisChart] = [
                                                        'fromCoordinate' => $fromCoordinate,
                                                        'fromOffsetX' => $fromOffsetX,
                                                        'fromOffsetY' => $fromOffsetY,
                                                        'toCoordinate' => $toCoordinate,
                                                        'toOffsetX' => $toOffsetX,
                                                        'toOffsetY' => $toOffsetY,
                                                        'worksheetTitle' => $docSheet->getTitle(),
                                                    ];
                                                }
                                            }
                                        }
                                        if ($xmlDrawingChildren->absoluteAnchor) {
                                            foreach ($xmlDrawingChildren->absoluteAnchor as $absoluteAnchor) {
                                                if (($this->includeCharts) && ($absoluteAnchor->graphicFrame)) {
                                                    $graphic = $absoluteAnchor->graphicFrame->children(Namespaces::DRAWINGML)->graphic;
                                                    /** @var SimpleXMLElement $chartRef */
                                                    $chartRef = $graphic->graphicData->children(Namespaces::CHART)->chart;
                                                    $thisChart = (string) self::getAttributes($chartRef, $xmlNamespaceBase);
                                                    $width = Drawing::EMUToPixels((int) self::getArrayItem(self::getAttributes($absoluteAnchor->ext), 'cx')[0]);
                                                    $height = Drawing::EMUToPixels((int) self::getArrayItem(self::getAttributes($absoluteAnchor->ext), 'cy')[0]);

                                                    $chartDetails[$docSheet->getTitle() . '!' . $thisChart] = [
                                                        'fromCoordinate' => 'A1',
                                                        'fromOffsetX' => 0,
                                                        'fromOffsetY' => 0,
                                                        'width' => $width,
                                                        'height' => $height,
                                                        'worksheetTitle' => $docSheet->getTitle(),
                                                    ];
                                                }
                                            }
                                        }
                                        if (empty($relsDrawing) && $xmlDrawing->count() == 0) {
                                            // Save Drawing without rels and children as unparsed
                                            $unparsedDrawings[$drawingRelId] = $xmlDrawing->asXML();
                                        }
                                    }

                                    // store original rId of drawing files
                                    $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingOriginalIds'] = [];
                                    foreach ($relsWorksheet->Relationship as $ele) {
                                        if ((string) $ele['Type'] === "$xmlNamespaceBase/drawing") {
                                            $drawingRelId = (string) $ele['Id'];
                                            $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingOriginalIds'][(string) $ele['Target']] = $drawingRelId;
                                            if (isset($unparsedDrawings[$drawingRelId])) {
                                                $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['Drawings'][$drawingRelId] = $unparsedDrawings[$drawingRelId];
                                            }
                                        }
                                    }

                                    // unparsed drawing AlternateContent
                                    $xmlAltDrawing = $this->loadZip($fileDrawing, Namespaces::COMPATIBILITY);

                                    if ($xmlAltDrawing->AlternateContent) {
                                        foreach ($xmlAltDrawing->AlternateContent as $alternateContent) {
                                            $alternateContent = self::testSimpleXml($alternateContent);
                                            $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingAlternateContents'][] = $alternateContent->asXML();
                                        }
                                    }
                                }
                            }

                            $this->readFormControlProperties($excel, $dir, $fileWorksheet, $docSheet, $unparsedLoadedData);
                            $this->readPrinterSettings($excel, $dir, $fileWorksheet, $docSheet, $unparsedLoadedData);

                            // Loop through definedNames
                            if ($xmlWorkbook->definedNames) {
                                foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
                                    // Extract range
                                    $extractedRange = (string) $definedName;
                                    if (($spos = strpos($extractedRange, '!')) !== false) {
                                        $extractedRange = substr($extractedRange, 0, $spos) . str_replace('$', '', substr($extractedRange, $spos));
                                    } else {
                                        $extractedRange = str_replace('$', '', $extractedRange);
                                    }

                                    // Valid range?
                                    if ($extractedRange == '') {
                                        continue;
                                    }

                                    // Some definedNames are only applicable if we are on the same sheet...
                                    if ((string) $definedName['localSheetId'] != '' && (string) $definedName['localSheetId'] == $oldSheetId) {
                                        // Switch on type
                                        switch ((string) $definedName['name']) {
                                            case '_xlnm._FilterDatabase':
                                                if ((string) $definedName['hidden'] !== '1') {
                                                    $extractedRange = explode(',', $extractedRange);
                                                    foreach ($extractedRange as $range) {
                                                        $autoFilterRange = $range;
                                                        if (strpos($autoFilterRange, ':') !== false) {
                                                            $docSheet->getAutoFilter()->setRange($autoFilterRange);
                                                        }
                                                    }
                                                }

                                                break;
                                            case '_xlnm.Print_Titles':
                                                // Split $extractedRange
                                                $extractedRange = explode(',', $extractedRange);

                                                // Set print titles
                                                foreach ($extractedRange as $range) {
                                                    $matches = [];
                                                    $range = str_replace('$', '', $range);

                                                    // check for repeating columns, e g. 'A:A' or 'A:D'
                                                    if (preg_match('/!?([A-Z]+)\:([A-Z]+)$/', $range, $matches)) {
                                                        $docSheet->getPageSetup()->setColumnsToRepeatAtLeft([$matches[1], $matches[2]]);
                                                    } elseif (preg_match('/!?(\d+)\:(\d+)$/', $range, $matches)) {
                                                        // check for repeating rows, e.g. '1:1' or '1:5'
                                                        $docSheet->getPageSetup()->setRowsToRepeatAtTop([$matches[1], $matches[2]]);
                                                    }
                                                }

                                                break;
                                            case '_xlnm.Print_Area':
                                                $rangeSets = preg_split("/('?(?:.*?)'?(?:![A-Z0-9]+:[A-Z0-9]+)),?/", $extractedRange, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                                                $newRangeSets = [];
                                                foreach ($rangeSets as $rangeSet) {
                                                    [$sheetName, $rangeSet] = Worksheet::extractSheetTitle($rangeSet, true);
                                                    if (strpos($rangeSet, ':') === false) {
                                                        $rangeSet = $rangeSet . ':' . $rangeSet;
                                                    }
                                                    $newRangeSets[] = str_replace('$', '', $rangeSet);
                                                }
                                                $docSheet->getPageSetup()->setPrintArea(implode(',', $newRangeSets));

                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                }
                            }

                            // Next sheet id
                            ++$sheetId;
                        }

                        // Loop through definedNames
                        if ($xmlWorkbook->definedNames) {
                            foreach ($xmlWorkbook->definedNames->definedName as $definedName) {
                                // Extract range
                                $extractedRange = (string) $definedName;

                                // Valid range?
                                if ($extractedRange == '') {
                                    continue;
                                }

                                // Some definedNames are only applicable if we are on the same sheet...
                                if ((string) $definedName['localSheetId'] != '') {
                                    // Local defined name
                                    // Switch on type
                                    switch ((string) $definedName['name']) {
                                        case '_xlnm._FilterDatabase':
                                        case '_xlnm.Print_Titles':
                                        case '_xlnm.Print_Area':
                                            break;
                                        default:
                                            if ($mapSheetId[(int) $definedName['localSheetId']] !== null) {
                                                $range = Worksheet::extractSheetTitle((string) $definedName, true);
                                                $scope = $excel->getSheet($mapSheetId[(int) $definedName['localSheetId']]);
                                                if (strpos((string) $definedName, '!') !== false) {
                                                    $range[0] = str_replace("''", "'", $range[0]);
                                                    $range[0] = str_replace("'", '', $range[0]);
                                                    if ($worksheet = $excel->getSheetByName($range[0])) {
                                                        $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $worksheet, $extractedRange, true, $scope));
                                                    } else {
                                                        $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $scope, $extractedRange, true, $scope));
                                                    }
                                                } else {
                                                    $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $scope, $extractedRange, true));
                                                }
                                            }

                                            break;
                                    }
                                } elseif (!isset($definedName['localSheetId'])) {
                                    $definedRange = (string) $definedName;
                                    // "Global" definedNames
                                    $locatedSheet = null;
                                    if (strpos((string) $definedName, '!') !== false) {
                                        // Modify range, and extract the first worksheet reference
                                        // Need to split on a comma or a space if not in quotes, and extract the first part.
                                        $definedNameValueParts = preg_split("/[ ,](?=([^']*'[^']*')*[^']*$)/miuU", $definedRange);
                                        // Extract sheet name
                                        [$extractedSheetName] = Worksheet::extractSheetTitle((string) $definedNameValueParts[0], true);
                                        $extractedSheetName = trim($extractedSheetName, "'");

                                        // Locate sheet
                                        $locatedSheet = $excel->getSheetByName($extractedSheetName);
                                    }

                                    if ($locatedSheet === null && !DefinedName::testIfFormula($definedRange)) {
                                        $definedRange = '#REF!';
                                    }
                                    $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $locatedSheet, $definedRange, false));
                                }
                            }
                        }
                    }

                    (new WorkbookView($excel))->viewSettings($xmlWorkbook, $mainNS, $mapSheetId, $this->readDataOnly);

                    break;
            }
        }

        if (!$this->readDataOnly) {
            $contentTypes = $this->loadZip('[Content_Types].xml');

            // Default content types
            foreach ($contentTypes->Default as $contentType) {
                switch ($contentType['ContentType']) {
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.printerSettings':
                        $unparsedLoadedData['default_content_types'][(string) $contentType['Extension']] = (string) $contentType['ContentType'];

                        break;
                }
            }

            // Override content types
            foreach ($contentTypes->Override as $contentType) {
                switch ($contentType['ContentType']) {
                    case 'application/vnd.openxmlformats-officedocument.drawingml.chart+xml':
                        if ($this->includeCharts) {
                            $chartEntryRef = ltrim((string) $contentType['PartName'], '/');
                            $chartElements = $this->loadZip($chartEntryRef);
                            $objChart = Chart::readChart($chartElements, basename($chartEntryRef, '.xml'));
                            if (isset($charts[$chartEntryRef])) {
                                $chartPositionRef = $charts[$chartEntryRef]['sheet'] . '!' . $charts[$chartEntryRef]['id'];
                                if (isset($chartDetails[$chartPositionRef])) {
                                    $excel->getSheetByName($charts[$chartEntryRef]['sheet'])->addChart($objChart);
                                    $objChart->setWorksheet($excel->getSheetByName($charts[$chartEntryRef]['sheet']));
                                    // For oneCellAnchor or absoluteAnchor positioned charts,
                                    //     toCoordinate is not in the data. Does it need to be calculated?
                                    if (array_key_exists('toCoordinate', $chartDetails[$chartPositionRef])) {
                                        // twoCellAnchor
                                        $objChart->setTopLeftPosition($chartDetails[$chartPositionRef]['fromCoordinate'], $chartDetails[$chartPositionRef]['fromOffsetX'], $chartDetails[$chartPositionRef]['fromOffsetY']);
                                        $objChart->setBottomRightPosition($chartDetails[$chartPositionRef]['toCoordinate'], $chartDetails[$chartPositionRef]['toOffsetX'], $chartDetails[$chartPositionRef]['toOffsetY']);
                                    } else {
                                        // oneCellAnchor or absoluteAnchor (e.g. Chart sheet)
                                        $objChart->setTopLeftPosition($chartDetails[$chartPositionRef]['fromCoordinate'], $chartDetails[$chartPositionRef]['fromOffsetX'], $chartDetails[$chartPositionRef]['fromOffsetY']);
                                        $objChart->setBottomRightPosition('', $chartDetails[$chartPositionRef]['width'], $chartDetails[$chartPositionRef]['height']);
                                    }
                                }
                            }
                        }

                        break;

                    // unparsed
                    case 'application/vnd.ms-excel.controlproperties+xml':
                        $unparsedLoadedData['override_content_types'][(string) $contentType['PartName']] = (string) $contentType['ContentType'];

                        break;
                }
            }
        }

        $excel->setUnparsedLoadedData($unparsedLoadedData);

        $zip->close();

        return $excel;
    }

    /**
     * @return RichText
     */
    private function parseRichText(?SimpleXMLElement $is)
    {
        $value = new RichText();

        if (isset($is->t)) {
            $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $is->t));
        } else {
            if (is_object($is->r)) {

                /** @var SimpleXMLElement $run */
                foreach ($is->r as $run) {
                    if (!isset($run->rPr)) {
                        $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $run->t));
                    } else {
                        $objText = $value->createTextRun(StringHelper::controlCharacterOOXML2PHP((string) $run->t));

                        if (isset($run->rPr->rFont)) {
                            $attr = $run->rPr->rFont->attributes();
                            if (isset($attr['val'])) {
                                $objText->getFont()->setName((string) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->sz)) {
                            $attr = $run->rPr->sz->attributes();
                            if (isset($attr['val'])) {
                                $objText->getFont()->setSize((float) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->color)) {
                            $objText->getFont()->setColor(new Color($this->styleReader->readColor($run->rPr->color)));
                        }
                        if (isset($run->rPr->b)) {
                            $attr = $run->rPr->b->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val'])) ||
                                (!isset($attr['val']))
                            ) {
                                $objText->getFont()->setBold(true);
                            }
                        }
                        if (isset($run->rPr->i)) {
                            $attr = $run->rPr->i->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val'])) ||
                                (!isset($attr['val']))
                            ) {
                                $objText->getFont()->setItalic(true);
                            }
                        }
                        if (isset($run->rPr->vertAlign)) {
                            $attr = $run->rPr->vertAlign->attributes();
                            if (isset($attr['val'])) {
                                $vertAlign = strtolower((string) $attr['val']);
                                if ($vertAlign == 'superscript') {
                                    $objText->getFont()->setSuperscript(true);
                                }
                                if ($vertAlign == 'subscript') {
                                    $objText->getFont()->setSubscript(true);
                                }
                            }
                        }
                        if (isset($run->rPr->u)) {
                            $attr = $run->rPr->u->attributes();
                            if (!isset($attr['val'])) {
                                $objText->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
                            } else {
                                $objText->getFont()->setUnderline((string) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->strike)) {
                            $attr = $run->rPr->strike->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val'])) ||
                                (!isset($attr['val']))
                            ) {
                                $objText->getFont()->setStrikethrough(true);
                            }
                        }
                    }
                }
            }
        }

        return $value;
    }

    private function readRibbon(Spreadsheet $excel, string $customUITarget, ZipArchive $zip): void
    {
        $baseDir = dirname($customUITarget);
        $nameCustomUI = basename($customUITarget);
        // get the xml file (ribbon)
        $localRibbon = $this->getFromZipArchive($zip, $customUITarget);
        $customUIImagesNames = [];
        $customUIImagesBinaries = [];
        // something like customUI/_rels/customUI.xml.rels
        $pathRels = $baseDir . '/_rels/' . $nameCustomUI . '.rels';
        $dataRels = $this->getFromZipArchive($zip, $pathRels);
        if ($dataRels) {
            // exists and not empty if the ribbon have some pictures (other than internal MSO)
            $UIRels = simplexml_load_string(
                $this->securityScanner->scan($dataRels),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );
            if (false !== $UIRels) {
                // we need to save id and target to avoid parsing customUI.xml and "guess" if it's a pseudo callback who load the image
                foreach ($UIRels->Relationship as $ele) {
                    if ((string) $ele['Type'] === Namespaces::SCHEMA_OFFICE_DOCUMENT . '/image') {
                        // an image ?
                        $customUIImagesNames[(string) $ele['Id']] = (string) $ele['Target'];
                        $customUIImagesBinaries[(string) $ele['Target']] = $this->getFromZipArchive($zip, $baseDir . '/' . (string) $ele['Target']);
                    }
                }
            }
        }
        if ($localRibbon) {
            $excel->setRibbonXMLData($customUITarget, $localRibbon);
            if (count($customUIImagesNames) > 0 && count($customUIImagesBinaries) > 0) {
                $excel->setRibbonBinObjects($customUIImagesNames, $customUIImagesBinaries);
            } else {
                $excel->setRibbonBinObjects(null, null);
            }
        } else {
            $excel->setRibbonXMLData(null, null);
            $excel->setRibbonBinObjects(null, null);
        }
    }

    private static function getArrayItem($array, $key = 0)
    {
        return $array[$key] ?? null;
    }

    private static function dirAdd($base, $add)
    {
        return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
    }

    private static function toCSSArray($style)
    {
        $style = self::stripWhiteSpaceFromStyleString($style);

        $temp = explode(';', $style);
        $style = [];
        foreach ($temp as $item) {
            $item = explode(':', $item);

            if (strpos($item[1], 'px') !== false) {
                $item[1] = str_replace('px', '', $item[1]);
            }
            if (strpos($item[1], 'pt') !== false) {
                $item[1] = str_replace('pt', '', $item[1]);
                $item[1] = Font::fontSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'in') !== false) {
                $item[1] = str_replace('in', '', $item[1]);
                $item[1] = Font::inchSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'cm') !== false) {
                $item[1] = str_replace('cm', '', $item[1]);
                $item[1] = Font::centimeterSizeToPixels($item[1]);
            }

            $style[$item[0]] = $item[1];
        }

        return $style;
    }

    public static function stripWhiteSpaceFromStyleString($string)
    {
        return trim(str_replace(["\r", "\n", ' '], '', $string), ';');
    }

    private static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === 'true' || $value === 'TRUE';
    }

    /**
     * @param array $hyperlinks
     */
    private function readHyperLinkDrawing(\PhpOffice\PhpSpreadsheet\Worksheet\Drawing $objDrawing, SimpleXMLElement $cellAnchor, $hyperlinks): void
    {
        $hlinkClick = $cellAnchor->pic->nvPicPr->cNvPr->children(Namespaces::DRAWINGML)->hlinkClick;

        if ($hlinkClick->count() === 0) {
            return;
        }

        $hlinkId = (string) self::getAttributes($hlinkClick, Namespaces::SCHEMA_OFFICE_DOCUMENT)['id'];
        $hyperlink = new Hyperlink(
            $hyperlinks[$hlinkId],
            (string) self::getArrayItem(self::getAttributes($cellAnchor->pic->nvPicPr->cNvPr), 'name')
        );
        $objDrawing->setHyperlink($hyperlink);
    }

    private function readProtection(Spreadsheet $excel, SimpleXMLElement $xmlWorkbook): void
    {
        if (!$xmlWorkbook->workbookProtection) {
            return;
        }

        $excel->getSecurity()->setLockRevision(self::getLockValue($xmlWorkbook->workbookProtection, 'lockRevision'));
        $excel->getSecurity()->setLockStructure(self::getLockValue($xmlWorkbook->workbookProtection, 'lockStructure'));
        $excel->getSecurity()->setLockWindows(self::getLockValue($xmlWorkbook->workbookProtection, 'lockWindows'));

        if ($xmlWorkbook->workbookProtection['revisionsPassword']) {
            $excel->getSecurity()->setRevisionsPassword(
                (string) $xmlWorkbook->workbookProtection['revisionsPassword'],
                true
            );
        }

        if ($xmlWorkbook->workbookProtection['workbookPassword']) {
            $excel->getSecurity()->setWorkbookPassword(
                (string) $xmlWorkbook->workbookProtection['workbookPassword'],
                true
            );
        }
    }

    private static function getLockValue(SimpleXmlElement $protection, string $key): ?bool
    {
        $returnValue = null;
        $protectKey = $protection[$key];
        if (!empty($protectKey)) {
            $protectKey = (string) $protectKey;
            $returnValue = $protectKey !== 'false' && (bool) $protectKey;
        }

        return $returnValue;
    }

    private function readFormControlProperties(Spreadsheet $excel, $dir, $fileWorksheet, $docSheet, array &$unparsedLoadedData): void
    {
        $zip = $this->zip;
        if (!$zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
            return;
        }

        $filename = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';
        $relsWorksheet = $this->loadZipNoNamespace($filename, Namespaces::RELATIONSHIPS);
        $ctrlProps = [];
        foreach ($relsWorksheet->Relationship as $ele) {
            if ((string) $ele['Type'] === Namespaces::SCHEMA_OFFICE_DOCUMENT . '/ctrlProp') {
                $ctrlProps[(string) $ele['Id']] = $ele;
            }
        }

        $unparsedCtrlProps = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['ctrlProps'];
        foreach ($ctrlProps as $rId => $ctrlProp) {
            $rId = substr($rId, 3); // rIdXXX
            $unparsedCtrlProps[$rId] = [];
            $unparsedCtrlProps[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $ctrlProp['Target']);
            $unparsedCtrlProps[$rId]['relFilePath'] = (string) $ctrlProp['Target'];
            $unparsedCtrlProps[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedCtrlProps[$rId]['filePath']));
        }
        unset($unparsedCtrlProps);
    }

    private function readPrinterSettings(Spreadsheet $excel, $dir, $fileWorksheet, $docSheet, array &$unparsedLoadedData): void
    {
        $zip = $this->zip;
        if (!$zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
            return;
        }

        $filename = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';
        $relsWorksheet = $this->loadZipNoNamespace($filename, Namespaces::RELATIONSHIPS);
        $sheetPrinterSettings = [];
        foreach ($relsWorksheet->Relationship as $ele) {
            if ((string) $ele['Type'] === Namespaces::SCHEMA_OFFICE_DOCUMENT . '/printerSettings') {
                $sheetPrinterSettings[(string) $ele['Id']] = $ele;
            }
        }

        $unparsedPrinterSettings = &$unparsedLoadedData['sheets'][$docSheet->getCodeName()]['printerSettings'];
        foreach ($sheetPrinterSettings as $rId => $printerSettings) {
            $rId = substr($rId, 3) . 'ps'; // rIdXXX, add 'ps' suffix to avoid identical resource identifier collision with unparsed vmlDrawing
            $unparsedPrinterSettings[$rId] = [];
            $unparsedPrinterSettings[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $printerSettings['Target']);
            $unparsedPrinterSettings[$rId]['relFilePath'] = (string) $printerSettings['Target'];
            $unparsedPrinterSettings[$rId]['content'] = $this->securityScanner->scan($this->getFromZipArchive($zip, $unparsedPrinterSettings[$rId]['filePath']));
        }
        unset($unparsedPrinterSettings);
    }

    private function getWorkbookBaseName(): array
    {
        $workbookBasename = '';
        $xmlNamespaceBase = '';

        // check if it is an OOXML archive
        $rels = $this->loadZip(self::INITIAL_FILE);
        foreach ($rels->children(Namespaces::RELATIONSHIPS)->Relationship as $rel) {
            $rel = self::getAttributes($rel);
            $type = (string) $rel['Type'];
            switch ($type) {
                case Namespaces::OFFICE_DOCUMENT:
                case Namespaces::PURL_OFFICE_DOCUMENT:
                    $basename = basename((string) $rel['Target']);
                    $xmlNamespaceBase = dirname($type);
                    if (preg_match('/workbook.*\.xml/', $basename)) {
                        $workbookBasename = $basename;
                    }

                    break;
            }
        }

        return [$workbookBasename, $xmlNamespaceBase];
    }

    private function readSheetProtection(Worksheet $docSheet, SimpleXMLElement $xmlSheet): void
    {
        if ($this->readDataOnly || !$xmlSheet->sheetProtection) {
            return;
        }

        $algorithmName = (string) $xmlSheet->sheetProtection['algorithmName'];
        $protection = $docSheet->getProtection();
        $protection->setAlgorithm($algorithmName);

        if ($algorithmName) {
            $protection->setPassword((string) $xmlSheet->sheetProtection['hashValue'], true);
            $protection->setSalt((string) $xmlSheet->sheetProtection['saltValue']);
            $protection->setSpinCount((int) $xmlSheet->sheetProtection['spinCount']);
        } else {
            $protection->setPassword((string) $xmlSheet->sheetProtection['password'], true);
        }

        if ($xmlSheet->protectedRanges->protectedRange) {
            foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
                $docSheet->protectCells((string) $protectedRange['sqref'], (string) $protectedRange['password'], true);
            }
        }
    }

    private function readAutoFilterTables(
        SimpleXMLElement $xmlSheet,
        Worksheet $docSheet,
        string $dir,
        string $fileWorksheet,
        ZipArchive $zip
    ): void {
        if ($xmlSheet && $xmlSheet->autoFilter) {
            // In older files, autofilter structure is defined in the worksheet file
            (new AutoFilter($docSheet, $xmlSheet))->load();
        } elseif ($xmlSheet && $xmlSheet->tableParts && $xmlSheet->tableParts['count'] > 0) {
            // But for Office365, MS decided to make it all just a bit more complicated
            $this->readAutoFilterTablesInTablesFile($xmlSheet, $dir, $fileWorksheet, $zip, $docSheet);
        }
    }

    private function readAutoFilterTablesInTablesFile(
        SimpleXMLElement $xmlSheet,
        string $dir,
        string $fileWorksheet,
        ZipArchive $zip,
        Worksheet $docSheet
    ): void {
        foreach ($xmlSheet->tableParts->tablePart as $tablePart) {
            $relation = self::getAttributes($tablePart, Namespaces::SCHEMA_OFFICE_DOCUMENT);
            $tablePartRel = (string) $relation['id'];
            $relationsFileName = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';

            if ($zip->locateName($relationsFileName)) {
                $relsTableReferences = $this->loadZip($relationsFileName, Namespaces::RELATIONSHIPS);
                foreach ($relsTableReferences->Relationship as $relationship) {
                    $relationshipAttributes = self::getAttributes($relationship, '');

                    if ((string) $relationshipAttributes['Id'] === $tablePartRel) {
                        $relationshipFileName = (string) $relationshipAttributes['Target'];
                        $relationshipFilePath = dirname("$dir/$fileWorksheet") . '/' . $relationshipFileName;
                        $relationshipFilePath = File::realpath($relationshipFilePath);

                        if ($this->fileExistsInArchive($this->zip, $relationshipFilePath)) {
                            $autoFilter = $this->loadZip($relationshipFilePath);
                            (new AutoFilter($docSheet, $autoFilter))->load();
                        }
                    }
                }
            }
        }
    }

    private static function extractStyles(?SimpleXMLElement $sxml, string $node1, string $node2): array
    {
        $array = [];
        if ($sxml && $sxml->{$node1}->{$node2}) {
            foreach ($sxml->{$node1}->{$node2} as $node) {
                $array[] = $node;
            }
        }

        return $array;
    }

    private static function extractPalette(?SimpleXMLElement $sxml): array
    {
        $array = [];
        if ($sxml && $sxml->colors->indexedColors) {
            foreach ($sxml->colors->indexedColors->rgbColor as $node) {
                if ($node !== null) {
                    $attr = $node->attributes();
                    if (isset($attr['rgb'])) {
                        $array[] = (string) $attr['rgb'];
                    }
                }
            }
        }

        return (count($array) === 64) ? $array : [];
    }
}
