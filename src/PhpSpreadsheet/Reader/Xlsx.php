<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Comment;
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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SharedFormula;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViewOptions;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SheetViews;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Styles;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\TableReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\WorkbookView;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use Stringable;
use Throwable;
use XMLReader;
use ZipArchive;

class Xlsx extends BaseReader
{
    const INITIAL_FILE = '_rels/.rels';

    /**
     * ReferenceHelper instance.
     */
    private ReferenceHelper $referenceHelper;

    private ZipArchive $zip;

    private Styles $styleReader;

    private array $sharedFormulae = [];

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

    public static function testSimpleXml(mixed $value): SimpleXMLElement
    {
        return ($value instanceof SimpleXMLElement) ? $value : new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    }

    public static function getAttributes(?SimpleXMLElement $value, string $ns = ''): SimpleXMLElement
    {
        return self::testSimpleXml($value === null ? $value : $value->attributes($ns));
    }

    // Phpstan thinks, correctly, that xpath can return false.
    private static function xpathNoFalse(SimpleXMLElement $sxml, string $path): array
    {
        return self::falseToArray($sxml->xpath($path));
    }

    public static function falseToArray(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function loadZip(string $filename, string $ns = '', bool $replaceUnclosedBr = false): SimpleXMLElement
    {
        $contents = $this->getFromZipArchive($this->zip, $filename);
        if ($replaceUnclosedBr) {
            $contents = str_replace('<br>', '<br/>', $contents);
        }
        $rels = @simplexml_load_string(
            $this->getSecurityScannerOrThrow()->scan($contents),
            'SimpleXMLElement',
            0,
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
            $this->getSecurityScannerOrThrow()->scan($contents),
            'SimpleXMLElement',
            0,
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

    private const REL_TO_CHART = [
        Namespaces::PURL_RELATIONSHIPS => Namespaces::PURL_CHART,
    ];

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object.
     */
    public function listWorksheetNames(string $filename): array
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
     */
    public function listWorksheetInfo(string $filename): array
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
                $relsWorkbook = $this->loadZip("$dir/_rels/" . basename($relTarget) . '.rels', Namespaces::RELATIONSHIPS);

                $worksheets = [];
                foreach ($relsWorkbook->Relationship as $elex) {
                    $ele = self::getAttributes($elex);
                    if (
                        ((string) $ele['Type'] === "$namespace/worksheet")
                        || ((string) $ele['Type'] === "$namespace/chartsheet")
                    ) {
                        $worksheets[(string) $ele['Id']] = $ele['Target'];
                    }
                }

                $xmlWorkbook = $this->loadZip($relTarget, $mainNS);
                if ($xmlWorkbook->sheets) {
                    $dir = dirname($relTarget);

                    foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                        $tmpInfo = [
                            'worksheetName' => (string) self::getAttributes($eleSheet)['name'],
                            'lastColumnLetter' => 'A',
                            'lastColumnIndex' => 0,
                            'totalRows' => 0,
                            'totalColumns' => 0,
                        ];

                        $fileWorksheet = (string) $worksheets[self::getArrayItemString(self::getAttributes($eleSheet, $namespace), 'id')];
                        $fileWorksheetPath = str_starts_with($fileWorksheet, '/') ? substr($fileWorksheet, 1) : "$dir/$fileWorksheet";

                        $xml = new XMLReader();
                        $xml->xml(
                            $this->getSecurityScannerOrThrow()
                                ->scan(
                                    $this->getFromZipArchive(
                                        $this->zip,
                                        $fileWorksheetPath
                                    )
                                )
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

    private static function castToBoolean(SimpleXMLElement $c): bool
    {
        $value = isset($c->v) ? (string) $c->v : null;
        if ($value == '0') {
            return false;
        } elseif ($value == '1') {
            return true;
        }

        return (bool) $c->v;
    }

    private static function castToError(?SimpleXMLElement $c): ?string
    {
        return isset($c, $c->v) ? (string) $c->v : null;
    }

    private static function castToString(?SimpleXMLElement $c): ?string
    {
        return isset($c, $c->v) ? (string) $c->v : null;
    }

    public static function replacePrefixes(string $formula): string
    {
        return str_replace(['_xlfn.', '_xlws.'], '', $formula);
    }

    private function castToFormula(?SimpleXMLElement $c, string $r, string &$cellDataType, mixed &$value, mixed &$calculatedValue, string $castBaseType, bool $updateSharedCells = true): void
    {
        if ($c === null) {
            return;
        }
        $attr = $c->f->attributes();
        $cellDataType = DataType::TYPE_FORMULA;
        $formula = self::replacePrefixes((string) $c->f);
        $value = "=$formula";
        $calculatedValue = self::$castBaseType($c);

        // Shared formula?
        if (isset($attr['t']) && strtolower((string) $attr['t']) == 'shared') {
            $instance = (string) $attr['si'];

            if (!isset($this->sharedFormulae[(string) $attr['si']])) {
                $this->sharedFormulae[$instance] = new SharedFormula($r, $value);
            } elseif ($updateSharedCells === true) {
                // It's only worth the overhead of adjusting the shared formula for this cell if we're actually loading
                //     the cell, which may not be the case if we're using a read filter.
                $master = Coordinate::indexesFromString($this->sharedFormulae[$instance]->master());
                $current = Coordinate::indexesFromString($r);

                $difference = [0, 0];
                $difference[0] = $current[0] - $master[0];
                $difference[1] = $current[1] - $master[1];

                $value = $this->referenceHelper->updateFormulaReferences($this->sharedFormulae[$instance]->formula(), 'A1', $difference[0], $difference[1]);
            }
        }
    }

    private function fileExistsInArchive(ZipArchive $archive, string $fileName = ''): bool
    {
        // Root-relative paths
        if (str_contains($fileName, '//')) {
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

    private function getFromZipArchive(ZipArchive $archive, string $fileName = ''): string
    {
        // Root-relative paths
        if (str_contains($fileName, '//')) {
            $fileName = substr($fileName, strpos($fileName, '//') + 1);
        }
        // Relative paths generated by dirname($filename) when $filename
        // has no path (i.e.files in root of the zip archive)
        $fileName = (string) preg_replace('/^\.\//', '', $fileName);
        $fileName = File::realpath($fileName);

        // Sadly, some 3rd party xlsx generators don't use consistent case for filenaming
        //    so we need to load case-insensitively from the zip file

        $contents = $archive->getFromName($fileName, 0, ZipArchive::FL_NOCASE);

        // Apache POI fixes
        if ($contents === false) {
            $contents = $archive->getFromName(substr($fileName, 1), 0, ZipArchive::FL_NOCASE);
        }

        // Has the file been saved with Windoze directory separators rather than unix?
        if ($contents === false) {
            $contents = $archive->getFromName(str_replace('/', '\\', $fileName), 0, ZipArchive::FL_NOCASE);
        }

        return ($contents === false) ? '' : $contents;
    }

    /**
     * Loads Spreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        File::assertFile($filename, self::INITIAL_FILE);

        // Initialisations
        $excel = new Spreadsheet();
        $excel->setValueBinder($this->valueBinder);
        $excel->removeSheetByIndex(0);
        $addingFirstCellStyleXf = true;
        $addingFirstCellXf = true;

        $unparsedLoadedData = [];

        $this->zip = $zip = new ZipArchive();
        $zip->open($filename);

        //    Read the theme first, because we need the colour scheme when reading the styles
        [$workbookBasename, $xmlNamespaceBase] = $this->getWorkbookBaseName();
        $drawingNS = self::REL_TO_DRAWING[$xmlNamespaceBase] ?? Namespaces::DRAWINGML;
        $chartNS = self::REL_TO_CHART[$xmlNamespaceBase] ?? Namespaces::CHART;
        $wbRels = $this->loadZip("xl/_rels/{$workbookBasename}.rels", Namespaces::RELATIONSHIPS);
        $theme = null;
        $this->styleReader = new Styles();
        foreach ($wbRels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relTarget = (string) $rel['Target'];
            if (str_starts_with($relTarget, '/xl/')) {
                $relTarget = substr($relTarget, 4);
            }
            switch ($rel['Type']) {
                case "$xmlNamespaceBase/theme":
                    if (!$this->fileExistsInArchive($zip, "xl/{$relTarget}")) {
                        break; // issue3770
                    }
                    $themeOrderArray = ['lt1', 'dk1', 'lt2', 'dk2'];
                    $themeOrderAdditional = count($themeOrderArray);

                    $xmlTheme = $this->loadZip("xl/{$relTarget}", $drawingNS);
                    $xmlThemeName = self::getAttributes($xmlTheme);
                    $xmlTheme = $xmlTheme->children($drawingNS);
                    $themeName = (string) $xmlThemeName['name'];

                    $colourScheme = self::getAttributes($xmlTheme->themeElements->clrScheme);
                    $colourSchemeName = (string) $colourScheme['name'];
                    $excel->getTheme()->setThemeColorName($colourSchemeName);
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
                            $excel->getTheme()->setThemeColor($k, (string) $xmlColourData['lastClr']);
                        } elseif (isset($xmlColour->srgbClr)) {
                            $xmlColourData = self::getAttributes($xmlColour->srgbClr);
                            $themeColours[$themePos] = (string) $xmlColourData['val'];
                            $excel->getTheme()->setThemeColor($k, (string) $xmlColourData['val']);
                        }
                    }
                    $theme = new Theme($themeName, $colourSchemeName, $themeColours);
                    $this->styleReader->setTheme($theme);

                    $fontScheme = self::getAttributes($xmlTheme->themeElements->fontScheme);
                    $fontSchemeName = (string) $fontScheme['name'];
                    $excel->getTheme()->setThemeFontName($fontSchemeName);
                    $majorFonts = [];
                    $minorFonts = [];
                    $fontScheme = $xmlTheme->themeElements->fontScheme->children($drawingNS);
                    $majorLatin = self::getAttributes($fontScheme->majorFont->latin)['typeface'] ?? '';
                    $majorEastAsian = self::getAttributes($fontScheme->majorFont->ea)['typeface'] ?? '';
                    $majorComplexScript = self::getAttributes($fontScheme->majorFont->cs)['typeface'] ?? '';
                    $minorLatin = self::getAttributes($fontScheme->minorFont->latin)['typeface'] ?? '';
                    $minorEastAsian = self::getAttributes($fontScheme->minorFont->ea)['typeface'] ?? '';
                    $minorComplexScript = self::getAttributes($fontScheme->minorFont->cs)['typeface'] ?? '';

                    foreach ($fontScheme->majorFont->font as $xmlFont) {
                        $fontAttributes = self::getAttributes($xmlFont);
                        $script = (string) ($fontAttributes['script'] ?? '');
                        if (!empty($script)) {
                            $majorFonts[$script] = (string) ($fontAttributes['typeface'] ?? '');
                        }
                    }
                    foreach ($fontScheme->minorFont->font as $xmlFont) {
                        $fontAttributes = self::getAttributes($xmlFont);
                        $script = (string) ($fontAttributes['script'] ?? '');
                        if (!empty($script)) {
                            $minorFonts[$script] = (string) ($fontAttributes['typeface'] ?? '');
                        }
                    }
                    $excel->getTheme()->setMajorFontValues($majorLatin, $majorEastAsian, $majorComplexScript, $majorFonts);
                    $excel->getTheme()->setMinorFontValues($minorLatin, $minorEastAsian, $minorComplexScript, $minorFonts);

                    break;
            }
        }

        $rels = $this->loadZip(self::INITIAL_FILE, Namespaces::RELATIONSHIPS);

        $propertyReader = new PropertyReader($this->getSecurityScannerOrThrow(), $excel->getProperties());
        $charts = $chartDetails = [];
        foreach ($rels->Relationship as $relx) {
            $rel = self::getAttributes($relx);
            $relTarget = (string) $rel['Target'];
            // issue 3553
            if ($relTarget[0] === '/') {
                $relTarget = substr($relTarget, 1);
            }
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
                    $relsWorkbook = $this->loadZip("$dir/_rels/" . basename($relTarget) . '.rels', Namespaces::RELATIONSHIPS);
                    $relsWorkbook->registerXPathNamespace('rel', Namespaces::RELATIONSHIPS);

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
                    /** @var ?SimpleXMLElement */
                    $xpath = self::getArrayItem(self::xpathNoFalse($relsWorkbook, $relType));

                    if ($xpath === null) {
                        $xmlStyles = self::testSimpleXml(null);
                    } else {
                        $stylesTarget = (string) $xpath['Target'];
                        $stylesTarget = str_starts_with($stylesTarget, '/') ? substr($stylesTarget, 1) : "$dir/$stylesTarget";
                        $xmlStyles = $this->loadZip($stylesTarget, $mainNS);
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
                                    /** @var ?SimpleXMLElement */
                                    $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));

                                    if (isset($tmpNumFmt['formatCode'])) {
                                        $numFmt = (string) $tmpNumFmt['formatCode'];
                                    }
                                }

                                // We shouldn't override any of the built-in MS Excel values (values below id 164)
                                //  But there's a lot of naughty homebrew xlsx writers that do use "reserved" id values that aren't actually used
                                //  So we make allowance for them rather than lose formatting masks
                                if (
                                    $numFmt === null
                                    && (int) $xf['numFmtId'] < 164
                                    && NumberFormat::builtInFormatCode((int) $xf['numFmtId']) !== ''
                                ) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }
                            $quotePrefix = (bool) (string) ($xf['quotePrefix'] ?? '');

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
                                /** @var ?SimpleXMLElement */
                                $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
                                if (isset($tmpNumFmt['formatCode'])) {
                                    $numFmt = (string) $tmpNumFmt['formatCode'];
                                } elseif ((int) $xf['numFmtId'] < 165) {
                                    $numFmt = NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }

                            $quotePrefix = (bool) (string) ($xf['quotePrefix'] ?? '');

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

                    // Read content after setting the styles
                    $sharedStrings = [];
                    $relType = "rel:Relationship[@Type='"
                        //. Namespaces::SHARED_STRINGS
                        . "$xmlNamespaceBase/sharedStrings"
                        . "']";
                    /** @var ?SimpleXMLElement */
                    $xpath = self::getArrayItem($relsWorkbook->xpath($relType));

                    if ($xpath) {
                        $sharedStringsTarget = (string) $xpath['Target'];
                        $sharedStringsTarget = str_starts_with($sharedStringsTarget, '/') ? substr($sharedStringsTarget, 1) : "$dir/$sharedStringsTarget";
                        $xmlStrings = $this->loadZip($sharedStringsTarget, $mainNS);
                        if (isset($xmlStrings->si)) {
                            foreach ($xmlStrings->si as $val) {
                                if (isset($val->t)) {
                                    $sharedStrings[] = StringHelper::controlCharacterOOXML2PHP((string) $val->t);
                                } elseif (isset($val->r)) {
                                    $sharedStrings[] = $this->parseRichText($val);
                                } else {
                                    $sharedStrings[] = '';
                                }
                            }
                        }
                    }

                    $xmlWorkbook = $this->loadZipNoNamespace($relTarget, $mainNS);
                    $xmlWorkbookNS = $this->loadZip($relTarget, $mainNS);

                    // Set base date
                    $excel->setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
                    if ($xmlWorkbookNS->workbookPr) {
                        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
                        $attrs1904 = self::getAttributes($xmlWorkbookNS->workbookPr);
                        if (isset($attrs1904['date1904'])) {
                            if (self::boolean((string) $attrs1904['date1904'])) {
                                Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
                                $excel->setExcelCalendar(Date::CALENDAR_MAC_1904);
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
                        foreach ($xmlWorkbookNS->sheets->sheet as $eleSheet) {
                            $eleSheetAttr = self::getAttributes($eleSheet);
                            ++$oldSheetId;

                            // Check if sheet should be skipped
                            if (is_array($this->loadSheetsOnly) && !in_array((string) $eleSheetAttr['name'], $this->loadSheetsOnly)) {
                                ++$countSkippedSheets;
                                $mapSheetId[$oldSheetId] = null;

                                continue;
                            }

                            $sheetReferenceId = self::getArrayItemString(self::getAttributes($eleSheet, $xmlNamespaceBase), 'id');
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
                            // issue 3665 adds test for /.
                            // This broke XlsxRootZipFilesTest,
                            //  but Excel reports an error with that file.
                            //  Testing dir for . avoids this problem.
                            //  It might be better just to drop the test.
                            if ($fileWorksheet[0] == '/' && $dir !== '.') {
                                $fileWorksheet = substr($fileWorksheet, strlen($dir) + 2);
                            }
                            $xmlSheet = $this->loadZipNoNamespace("$dir/$fileWorksheet", $mainNS);
                            $xmlSheetNS = $this->loadZip("$dir/$fileWorksheet", $mainNS);

                            // Shared Formula table is unique to each Worksheet, so we need to reset it here
                            $this->sharedFormulae = [];

                            if (isset($eleSheetAttr['state']) && (string) $eleSheetAttr['state'] != '') {
                                $docSheet->setSheetState((string) $eleSheetAttr['state']);
                            }
                            if ($xmlSheetNS) {
                                $xmlSheetMain = $xmlSheetNS->children($mainNS);
                                // Setting Conditional Styles adjusts selected cells, so we need to execute this
                                //    before reading the sheet view data to get the actual selected cells
                                if (!$this->readDataOnly && ($xmlSheet->conditionalFormatting)) {
                                    (new ConditionalStyles($docSheet, $xmlSheet, $dxfs, $this->styleReader))->load();
                                }
                                if (!$this->readDataOnly && $xmlSheet->extLst) {
                                    (new ConditionalStyles($docSheet, $xmlSheet, $dxfs, $this->styleReader))->loadFromExt();
                                }
                                if (isset($xmlSheetMain->sheetViews, $xmlSheetMain->sheetViews->sheetView)) {
                                    $sheetViews = new SheetViews($xmlSheetMain->sheetViews->sheetView, $docSheet);
                                    $sheetViews->load();
                                }

                                $sheetViewOptions = new SheetViewOptions($docSheet, $xmlSheetNS);
                                $sheetViewOptions->load($this->readDataOnly, $this->styleReader);

                                (new ColumnAndRowAttributes($docSheet, $xmlSheetNS))
                                    ->load($this->getReadFilter(), $this->readDataOnly, $this->ignoreRowsWithNoCells);
                            }

                            $holdSelectedCells = $docSheet->getSelectedCells();
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
                                        $originalCellDataTypeNumeric = $cellDataType === '';
                                        $value = null;
                                        $calculatedValue = null;

                                        // Read cell?
                                        if ($this->getReadFilter() !== null) {
                                            $coordinates = Coordinate::coordinateFromString($r);

                                            if (!$this->getReadFilter()->readCell($coordinates[0], (int) $coordinates[1], $docSheet->getTitle())) {
                                                // Normally, just testing for the f attribute should identify this cell as containing a formula
                                                // that we need to read, even though it is outside of the filter range, in case it is a shared formula.
                                                // But in some cases, this attribute isn't set; so we need to delve a level deeper and look at
                                                // whether or not the cell has a child formula element that is shared.
                                                if (isset($cAttr->f) || (isset($c->f, $c->f->attributes()['t']) && strtolower((string) $c->f->attributes()['t']) === 'shared')) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToError', false);
                                                }
                                                ++$rowIndex;

                                                continue;
                                            }
                                        }

                                        // Read cell!
                                        switch ($cellDataType) {
                                            case DataType::TYPE_STRING:
                                                if ((string) $c->v != '') {
                                                    $value = $sharedStrings[(int) ($c->v)];

                                                    if ($value instanceof RichText) {
                                                        $value = clone $value;
                                                    }
                                                } else {
                                                    $value = '';
                                                }

                                                break;
                                            case DataType::TYPE_BOOL:
                                                if (!isset($c->f) || ((string) $c->f) === '') {
                                                    if (isset($c->v)) {
                                                        $value = self::castToBoolean($c);
                                                    } else {
                                                        $value = null;
                                                        $cellDataType = DataType::TYPE_NULL;
                                                    }
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToBoolean');
                                                    self::storeFormulaAttributes($c->f, $docSheet, $r);
                                                }

                                                break;
                                            case DataType::TYPE_STRING2:
                                                if (isset($c->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToString');
                                                    self::storeFormulaAttributes($c->f, $docSheet, $r);
                                                } else {
                                                     $value = self::castToString($c);
                                                }

                                                break;
                                            case DataType::TYPE_INLINE:
                                                if (isset($c->f)) {
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToError');
                                                    self::storeFormulaAttributes($c->f, $docSheet, $r);
                                                } else {
                                                    $value = $this->parseRichText($c->is);
                                                }

                                                break;
                                            case DataType::TYPE_ERROR:
                                                if (!isset($c->f)) {
                                                    $value = self::castToError($c);
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToError');
                                                    $eattr = $c->attributes();
                                                    if (isset($eattr['vm'])) {
                                                        if ($calculatedValue === ExcelError::VALUE()) {
                                                            $calculatedValue = ExcelError::SPILL();
                                                        }
                                                    }
                                                }

                                                break;
                                            default:
                                                if (!isset($c->f)) {
                                                    $value = self::castToString($c);
                                                    if (is_numeric($value)) {
                                                        $value += 0;
                                                        $cellDataType = DataType::TYPE_NUMERIC;
                                                    }
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, 'castToString');
                                                    if (is_numeric($calculatedValue)) {
                                                        $calculatedValue += 0;
                                                    }
                                                    self::storeFormulaAttributes($c->f, $docSheet, $r);
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
                                                $cell->setCalculatedValue($calculatedValue, $originalCellDataTypeNumeric);
                                            }

                                            // Style information?
                                            if (!$this->readDataOnly) {
                                                $cAttrS = (int) ($cAttr['s'] ?? 0);
                                                // no style index means 0, it seems
                                                $cAttrS = isset($styles[$cAttrS]) ? $cAttrS : 0;
                                                $cell->setXfIndex($cAttrS);
                                                // issue 3495
                                                if ($cellDataType === DataType::TYPE_FORMULA && $styles[$cAttrS]->quotePrefix === true) {
                                                    $holdSelected = $docSheet->getSelectedCells();
                                                    $cell->getStyle()->setQuotePrefix(false);
                                                    $docSheet->setSelectedCells($holdSelected);
                                                }
                                            }
                                        }
                                        ++$rowIndex;
                                    }
                                    ++$cIndex;
                                }
                            }
                            $docSheet->setSelectedCells($holdSelectedCells);
                            if ($xmlSheetNS && $xmlSheetNS->ignoredErrors) {
                                foreach ($xmlSheetNS->ignoredErrors->ignoredError as $ignoredErrorx) {
                                    $ignoredError = self::testSimpleXml($ignoredErrorx);
                                    $this->processIgnoredErrors($ignoredError, $docSheet);
                                }
                            }

                            if (!$this->readDataOnly && $xmlSheetNS && $xmlSheetNS->sheetProtection) {
                                $protAttr = $xmlSheetNS->sheetProtection->attributes() ?? [];
                                foreach ($protAttr as $key => $value) {
                                    $method = 'set' . ucfirst($key);
                                    $docSheet->getProtection()->$method(self::boolean((string) $value));
                                }
                            }

                            if ($xmlSheet) {
                                $this->readSheetProtection($docSheet, $xmlSheet);
                            }

                            if ($this->readDataOnly === false) {
                                $this->readAutoFilter($xmlSheetNS, $docSheet);
                                $this->readBackgroundImage($xmlSheetNS, $docSheet, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels');
                            }

                            $this->readTables($xmlSheetNS, $docSheet, $dir, $fileWorksheet, $zip, $mainNS);

                            if ($xmlSheetNS && $xmlSheetNS->mergeCells && $xmlSheetNS->mergeCells->mergeCell && !$this->readDataOnly) {
                                foreach ($xmlSheetNS->mergeCells->mergeCell as $mergeCellx) {
                                    $mergeCell = $mergeCellx->attributes();
                                    $mergeRef = (string) ($mergeCell['ref'] ?? '');
                                    if (str_contains($mergeRef, ':')) {
                                        $docSheet->mergeCells($mergeRef, Worksheet::MERGE_CELL_CONTENT_HIDE);
                                    }
                                }
                            }

                            if ($xmlSheet && !$this->readDataOnly) {
                                $unparsedLoadedData = (new PageSetup($docSheet, $xmlSheet))->load($unparsedLoadedData);
                            }

                            if ($xmlSheet !== false && isset($xmlSheet->extLst->ext)) {
                                foreach ($xmlSheet->extLst->ext as $extlst) {
                                    $extAttrs = $extlst->attributes() ?? [];
                                    $extUri = (string) ($extAttrs['uri'] ?? '');
                                    if ($extUri !== '{CCE6A557-97BC-4b89-ADB6-D9C93CAAB3DF}') {
                                        continue;
                                    }
                                    // Create dataValidations node if does not exists, maybe is better inside the foreach ?
                                    if (!$xmlSheet->dataValidations) {
                                        $xmlSheet->addChild('dataValidations');
                                    }

                                    foreach ($extlst->children(Namespaces::DATA_VALIDATIONS1)->dataValidations->dataValidation as $item) {
                                        $item = self::testSimpleXml($item);
                                        $node = self::testSimpleXml($xmlSheet->dataValidations)->addChild('dataValidation');
                                        foreach ($item->attributes() ?? [] as $attr) {
                                            $node->addAttribute($attr->getName(), $attr);
                                        }
                                        $node->addAttribute('sqref', $item->children(Namespaces::DATA_VALIDATIONS2)->sqref);
                                        if (isset($item->formula1)) {
                                            $childNode = $node->addChild('formula1');
                                            if ($childNode !== null) { // null should never happen
                                                // see https://github.com/phpstan/phpstan/issues/8236
                                                $childNode[0] = (string) $item->formula1->children(Namespaces::DATA_VALIDATIONS2)->f; // @phpstan-ignore-line
                                            }
                                        }
                                    }
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
                                if ($zip->locateName($relationsFileName) !== false) {
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
                                if ($zip->locateName($commentRelations) !== false) {
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
                                $vmlDrawingContents = [];

                                // Loop through VML comments
                                foreach ($vmlComments as $relName => $relPath) {
                                    // Load VML comments file
                                    $relPath = File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);

                                    try {
                                        // no namespace okay - processed with Xpath
                                        $vmlCommentsFile = $this->loadZip($relPath, '', true);
                                        $vmlCommentsFile->registerXPathNamespace('v', Namespaces::URN_VML);
                                    } catch (Throwable) {
                                        //Ignore unparsable vmlDrawings. Later they will be moved from $unparsedVmlDrawings to $unparsedLoadedData
                                        continue;
                                    }

                                    // Locate VML drawings image relations
                                    $drowingImages = [];
                                    $VMLDrawingsRelations = dirname($relPath) . '/_rels/' . basename($relPath) . '.rels';
                                    $vmlDrawingContents[$relName] = $this->getSecurityScannerOrThrow()->scan($this->getFromZipArchive($zip, $relPath));
                                    if ($zip->locateName($VMLDrawingsRelations) !== false) {
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
                                            $textHAlign = null;
                                            $fillImageRelId = null;
                                            $fillImageTitle = '';

                                            $clientData = $shape->xpath('.//x:ClientData');
                                            $textboxDirection = '';
                                            $textboxPath = $shape->xpath('.//v:textbox');
                                            $textbox = (string) ($textboxPath[0]['style'] ?? '');
                                            if (preg_match('/rtl/i', $textbox) === 1) {
                                                $textboxDirection = Comment::TEXTBOX_DIRECTION_RTL;
                                            } elseif (preg_match('/ltr/i', $textbox) === 1) {
                                                $textboxDirection = Comment::TEXTBOX_DIRECTION_LTR;
                                            }
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
                                                    $temp = $clientData->xpath('.//x:TextHAlign');
                                                    if (!empty($temp)) {
                                                        $textHAlign = strtolower($temp[0]);
                                                    }
                                                }
                                            }
                                            $rowx = (string) $row;
                                            $colx = (string) $column;
                                            if (is_numeric($rowx) && is_numeric($colx) && $textHAlign !== null) {
                                                $docSheet->getComment([1 + (int) $colx, 1 + (int) $rowx], false)->setAlignment((string) $textHAlign);
                                            }
                                            if (is_numeric($rowx) && is_numeric($colx) && $textboxDirection !== '') {
                                                $docSheet->getComment([1 + (int) $colx, 1 + (int) $rowx], false)->setTextboxDirection($textboxDirection);
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
                                                $comment = $docSheet->getComment([$column + 1, $row + 1]);
                                                $comment->getFillColor()->setRGB($fillColor);
                                                if (isset($drowingImages[$fillImageRelId])) {
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName($fillImageTitle);
                                                    $imagePath = str_replace(['../', '/xl/'], 'xl/', $drowingImages[$fillImageRelId]);
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
                                        $unparsedVmlDrawing[$rId]['content'] = $this->getSecurityScannerOrThrow()->scan($this->getFromZipArchive($zip, $unparsedVmlDrawing[$rId]['filePath']));
                                        unset($unparsedVmlDrawing);
                                    }
                                }

                                // Header/footer images
                                if ($xmlSheetNS && $xmlSheetNS->legacyDrawingHF) {
                                    $vmlHfRid = '';
                                    $vmlHfRidAttr = $xmlSheetNS->legacyDrawingHF->attributes(Namespaces::SCHEMA_OFFICE_DOCUMENT);
                                    if ($vmlHfRidAttr !== null && isset($vmlHfRidAttr['id'])) {
                                        $vmlHfRid = (string) $vmlHfRidAttr['id'][0];
                                    }
                                    if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels') !== false) {
                                        $relsWorksheet = $this->loadZipNoNamespace(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels', Namespaces::RELATIONSHIPS);
                                        $vmlRelationship = '';

                                        foreach ($relsWorksheet->Relationship as $ele) {
                                            if ((string) $ele['Type'] == Namespaces::VML && (string) $ele['Id'] === $vmlHfRid) {
                                                $vmlRelationship = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);

                                                break;
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

                                                if (array_key_exists((string) $imageData['relid'], $drawings)) {
                                                    $shapeId = (string) $shape['id'];
                                                    $hfImages[$shapeId] = new HeaderFooterDrawing();
                                                    if (isset($imageData['title'])) {
                                                        $hfImages[$shapeId]->setName((string) $imageData['title']);
                                                    }

                                                    $hfImages[$shapeId]->setPath('zip://' . File::realpath($filename) . '#' . $drawings[(string) $imageData['relid']], false, $zip);
                                                    $hfImages[$shapeId]->setResizeProportional(false);
                                                    $hfImages[$shapeId]->setWidth($style['width']);
                                                    $hfImages[$shapeId]->setHeight($style['height']);
                                                    if (isset($style['margin-left'])) {
                                                        $hfImages[$shapeId]->setOffsetX($style['margin-left']);
                                                    }
                                                    $hfImages[$shapeId]->setOffsetY($style['margin-top']);
                                                    $hfImages[$shapeId]->setResizeProportional(true);
                                                }
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
                            if (str_starts_with($drawingFilename, 'xl//xl/')) {
                                $drawingFilename = substr($drawingFilename, 4);
                            }
                            if (str_starts_with($drawingFilename, '/xl//xl/')) {
                                $drawingFilename = substr($drawingFilename, 5);
                            }
                            if ($zip->locateName($drawingFilename) !== false) {
                                $relsWorksheet = $this->loadZip($drawingFilename, Namespaces::RELATIONSHIPS);
                                $drawings = [];
                                foreach ($relsWorksheet->Relationship as $elex) {
                                    $ele = self::getAttributes($elex);
                                    if ((string) $ele['Type'] === "$xmlNamespaceBase/drawing") {
                                        $eleTarget = (string) $ele['Target'];
                                        if (str_starts_with($eleTarget, '/xl/')) {
                                            $drawings[(string) $ele['Id']] = substr($eleTarget, 1);
                                        } else {
                                            $drawings[(string) $ele['Id']] = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                        }
                                    }
                                }

                                if ($xmlSheetNS->drawing && !$this->readDataOnly) {
                                    $unparsedDrawings = [];
                                    $fileDrawing = null;
                                    foreach ($xmlSheetNS->drawing as $drawing) {
                                        $drawingRelId = self::getArrayItemString(self::getAttributes($drawing, $xmlNamespaceBase), 'id');
                                        $fileDrawing = $drawings[$drawingRelId];
                                        $drawingFilename = dirname($fileDrawing) . '/_rels/' . basename($fileDrawing) . '.rels';
                                        $relsDrawing = $this->loadZip($drawingFilename, Namespaces::RELATIONSHIPS);

                                        $images = [];
                                        $hyperlinks = [];
                                        if ($relsDrawing && $relsDrawing->Relationship) {
                                            foreach ($relsDrawing->Relationship as $elex) {
                                                $ele = self::getAttributes($elex);
                                                $eleType = (string) $ele['Type'];
                                                if ($eleType === Namespaces::HYPERLINK) {
                                                    $hyperlinks[(string) $ele['Id']] = (string) $ele['Target'];
                                                }
                                                if ($eleType === "$xmlNamespaceBase/image") {
                                                    $eleTarget = (string) $ele['Target'];
                                                    if (str_starts_with($eleTarget, '/xl/')) {
                                                        $eleTarget = substr($eleTarget, 1);
                                                        $images[(string) $ele['Id']] = $eleTarget;
                                                    } else {
                                                        $images[(string) $ele['Id']] = self::dirAdd($fileDrawing, $eleTarget);
                                                    }
                                                } elseif ($eleType === "$xmlNamespaceBase/chart") {
                                                    if ($this->includeCharts) {
                                                        $eleTarget = (string) $ele['Target'];
                                                        if (str_starts_with($eleTarget, '/xl/')) {
                                                            $index = substr($eleTarget, 1);
                                                        } else {
                                                            $index = self::dirAdd($fileDrawing, $eleTarget);
                                                        }
                                                        $charts[$index] = [
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
                                                $oneCellAnchor = self::testSimpleXml($oneCellAnchor);
                                                if ($oneCellAnchor->pic->blipFill) {
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $blip = $oneCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->blip;
                                                    if (isset($blip, $blip->alphaModFix)) {
                                                        $temp = (string) $blip->alphaModFix->attributes()->amt;
                                                        if (is_numeric($temp)) {
                                                            $objDrawing->setOpacity((int) $temp);
                                                        }
                                                    }
                                                    $xfrm = $oneCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->xfrm;
                                                    $outerShdw = $oneCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->effectLst->outerShdw;

                                                    $objDrawing->setName(self::getArrayItemString(self::getAttributes($oneCellAnchor->pic->nvPicPr->cNvPr), 'name'));
                                                    $objDrawing->setDescription(self::getArrayItemString(self::getAttributes($oneCellAnchor->pic->nvPicPr->cNvPr), 'descr'));
                                                    $embedImageKey = self::getArrayItemString(
                                                        self::getAttributes($blip, $xmlNamespaceBase),
                                                        'embed'
                                                    );
                                                    if (isset($images[$embedImageKey])) {
                                                        $objDrawing->setPath(
                                                            'zip://' . File::realpath($filename) . '#'
                                                            . $images[$embedImageKey],
                                                            false,
                                                            $zip
                                                        );
                                                    } else {
                                                        $linkImageKey = self::getArrayItemString(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'link'
                                                        );
                                                        if (isset($images[$linkImageKey])) {
                                                            $url = str_replace('xl/drawings/', '', $images[$linkImageKey]);
                                                            $objDrawing->setPath($url, false);
                                                        }
                                                        if ($objDrawing->getPath() === '') {
                                                            continue;
                                                        }
                                                    }
                                                    $objDrawing->setCoordinates(Coordinate::stringFromColumnIndex(((int) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1));

                                                    $objDrawing->setOffsetX((int) Drawing::EMUToPixels($oneCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);
                                                    $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($oneCellAnchor->ext), 'cx')));
                                                    $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($oneCellAnchor->ext), 'cy')));
                                                    if ($xfrm) {
                                                        $objDrawing->setRotation((int) Drawing::angleToDegrees(self::getArrayItemIntOrSxml(self::getAttributes($xfrm), 'rot')));
                                                        $objDrawing->setFlipVertical((bool) self::getArrayItem(self::getAttributes($xfrm), 'flipV'));
                                                        $objDrawing->setFlipHorizontal((bool) self::getArrayItem(self::getAttributes($xfrm), 'flipH'));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'dir')));
                                                        $shadow->setAlignment(self::getArrayItemString(self::getAttributes($outerShdw), 'algn'));
                                                        $clr = $outerShdw->srgbClr ?? $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItemString(self::getAttributes($clr), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem(self::getAttributes($clr->alpha), 'val') / 1000);
                                                    }

                                                    $this->readHyperLinkDrawing($objDrawing, $oneCellAnchor, $hyperlinks);

                                                    $objDrawing->setWorksheet($docSheet);
                                                } elseif ($this->includeCharts && $oneCellAnchor->graphicFrame) {
                                                    // Exported XLSX from Google Sheets positions charts with a oneCellAnchor
                                                    $coordinates = Coordinate::stringFromColumnIndex(((int) $oneCellAnchor->from->col) + 1) . ($oneCellAnchor->from->row + 1);
                                                    $offsetX = Drawing::EMUToPixels($oneCellAnchor->from->colOff);
                                                    $offsetY = Drawing::EMUToPixels($oneCellAnchor->from->rowOff);
                                                    $width = Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($oneCellAnchor->ext), 'cx'));
                                                    $height = Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($oneCellAnchor->ext), 'cy'));

                                                    $graphic = $oneCellAnchor->graphicFrame->children(Namespaces::DRAWINGML)->graphic;
                                                    $chartRef = $graphic->graphicData->children(Namespaces::CHART)->chart;
                                                    $thisChart = (string) self::getAttributes($chartRef, $xmlNamespaceBase);

                                                    $chartDetails[$docSheet->getTitle() . '!' . $thisChart] = [
                                                        'fromCoordinate' => $coordinates,
                                                        'fromOffsetX' => $offsetX,
                                                        'fromOffsetY' => $offsetY,
                                                        'width' => $width,
                                                        'height' => $height,
                                                        'worksheetTitle' => $docSheet->getTitle(),
                                                        'oneCellAnchor' => true,
                                                    ];
                                                }
                                            }
                                        }
                                        if ($xmlDrawingChildren->twoCellAnchor) {
                                            foreach ($xmlDrawingChildren->twoCellAnchor as $twoCellAnchor) {
                                                $twoCellAnchor = self::testSimpleXml($twoCellAnchor);
                                                if ($twoCellAnchor->pic->blipFill) {
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $blip = $twoCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->blip;
                                                    if (isset($blip, $blip->alphaModFix)) {
                                                        $temp = (string) $blip->alphaModFix->attributes()->amt;
                                                        if (is_numeric($temp)) {
                                                            $objDrawing->setOpacity((int) $temp);
                                                        }
                                                    }
                                                    if (isset($twoCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->srcRect)) {
                                                        $objDrawing->setSrcRect($twoCellAnchor->pic->blipFill->children(Namespaces::DRAWINGML)->srcRect->attributes());
                                                    }
                                                    $xfrm = $twoCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->xfrm;
                                                    $outerShdw = $twoCellAnchor->pic->spPr->children(Namespaces::DRAWINGML)->effectLst->outerShdw;
                                                    $editAs = $twoCellAnchor->attributes();
                                                    if (isset($editAs, $editAs['editAs'])) {
                                                        $objDrawing->setEditAs($editAs['editAs']);
                                                    }
                                                    $objDrawing->setName((string) self::getArrayItemString(self::getAttributes($twoCellAnchor->pic->nvPicPr->cNvPr), 'name'));
                                                    $objDrawing->setDescription(self::getArrayItemString(self::getAttributes($twoCellAnchor->pic->nvPicPr->cNvPr), 'descr'));
                                                    $embedImageKey = self::getArrayItemString(
                                                        self::getAttributes($blip, $xmlNamespaceBase),
                                                        'embed'
                                                    );
                                                    if (isset($images[$embedImageKey])) {
                                                        $objDrawing->setPath(
                                                            'zip://' . File::realpath($filename) . '#'
                                                            . $images[$embedImageKey],
                                                            false,
                                                            $zip
                                                        );
                                                    } else {
                                                        $linkImageKey = self::getArrayItemString(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'link'
                                                        );
                                                        if (isset($images[$linkImageKey])) {
                                                            $url = str_replace('xl/drawings/', '', $images[$linkImageKey]);
                                                            $objDrawing->setPath($url, false);
                                                        }
                                                        if ($objDrawing->getPath() === '') {
                                                            continue;
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
                                                        $objDrawing->setWidth(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($xfrm->ext), 'cx')));
                                                        $objDrawing->setHeight(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($xfrm->ext), 'cy')));
                                                        $objDrawing->setRotation(Drawing::angleToDegrees(self::getArrayItemIntOrSxml(self::getAttributes($xfrm), 'rot')));
                                                        $objDrawing->setFlipVertical((bool) self::getArrayItem(self::getAttributes($xfrm), 'flipV'));
                                                        $objDrawing->setFlipHorizontal((bool) self::getArrayItem(self::getAttributes($xfrm), 'flipH'));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'blurRad')));
                                                        $shadow->setDistance(Drawing::EMUToPixels(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'dist')));
                                                        $shadow->setDirection(Drawing::angleToDegrees(self::getArrayItemIntOrSxml(self::getAttributes($outerShdw), 'dir')));
                                                        $shadow->setAlignment(self::getArrayItemString(self::getAttributes($outerShdw), 'algn'));
                                                        $clr = $outerShdw->srgbClr ?? $outerShdw->prstClr;
                                                        $shadow->getColor()->setRGB(self::getArrayItemString(self::getAttributes($clr), 'val'));
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
                                                    $chartRef = $graphic->graphicData->children(Namespaces::CHART)->chart;
                                                    $thisChart = (string) self::getAttributes($chartRef, $xmlNamespaceBase);
                                                    $width = Drawing::EMUToPixels((int) self::getArrayItemString(self::getAttributes($absoluteAnchor->ext), 'cx')[0]);
                                                    $height = Drawing::EMUToPixels((int) self::getArrayItemString(self::getAttributes($absoluteAnchor->ext), 'cy')[0]);

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
                                    foreach ($relsWorksheet->Relationship as $elex) {
                                        $ele = self::getAttributes($elex);
                                        if ((string) $ele['Type'] === "$xmlNamespaceBase/drawing") {
                                            $drawingRelId = (string) $ele['Id'];
                                            $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['drawingOriginalIds'][(string) $ele['Target']] = $drawingRelId;
                                            if (isset($unparsedDrawings[$drawingRelId])) {
                                                $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['Drawings'][$drawingRelId] = $unparsedDrawings[$drawingRelId];
                                            }
                                        }
                                    }
                                    if ($xmlSheet->legacyDrawing && !$this->readDataOnly) {
                                        foreach ($xmlSheet->legacyDrawing as $drawing) {
                                            $drawingRelId = self::getArrayItemString(self::getAttributes($drawing, $xmlNamespaceBase), 'id');
                                            if (isset($vmlDrawingContents[$drawingRelId])) {
                                                if (self::onlyNoteVml($vmlDrawingContents[$drawingRelId]) === false) {
                                                    $unparsedLoadedData['sheets'][$docSheet->getCodeName()]['legacyDrawing'] = $vmlDrawingContents[$drawingRelId];
                                                }
                                            }
                                        }
                                    }

                                    // unparsed drawing AlternateContent
                                    $xmlAltDrawing = $this->loadZip((string) $fileDrawing, Namespaces::COMPATIBILITY);

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
                                                        if (str_contains($autoFilterRange, ':')) {
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
                                                $rangeSets = preg_split("/('?(?:.*?)'?(?:![A-Z0-9]+:[A-Z0-9]+)),?/", $extractedRange, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) ?: [];
                                                $newRangeSets = [];
                                                foreach ($rangeSets as $rangeSet) {
                                                    [, $rangeSet] = Worksheet::extractSheetTitle($rangeSet, true);
                                                    if (empty($rangeSet)) {
                                                        continue;
                                                    }
                                                    if (!str_contains($rangeSet, ':')) {
                                                        $rangeSet = $rangeSet . ':' . $rangeSet;
                                                    }
                                                    $newRangeSets[] = str_replace('$', '', $rangeSet);
                                                }
                                                if (count($newRangeSets) > 0) {
                                                    $docSheet->getPageSetup()->setPrintArea(implode(',', $newRangeSets));
                                                }

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
                                                $range = Worksheet::extractSheetTitle($extractedRange, true);
                                                $scope = $excel->getSheet($mapSheetId[(int) $definedName['localSheetId']]);
                                                if (str_contains((string) $definedName, '!')) {
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
                                    // "Global" definedNames
                                    $locatedSheet = null;
                                    if (str_contains((string) $definedName, '!')) {
                                        // Modify range, and extract the first worksheet reference
                                        // Need to split on a comma or a space if not in quotes, and extract the first part.
                                        $definedNameValueParts = preg_split("/[ ,](?=([^']*'[^']*')*[^']*$)/miuU", $extractedRange);
                                        if (is_array($definedNameValueParts)) {
                                            // Extract sheet name
                                            [$extractedSheetName] = Worksheet::extractSheetTitle((string) $definedNameValueParts[0], true);
                                            $extractedSheetName = trim((string) $extractedSheetName, "'");

                                            // Locate sheet
                                            $locatedSheet = $excel->getSheetByName($extractedSheetName);
                                        }
                                    }

                                    if ($locatedSheet === null && !DefinedName::testIfFormula($extractedRange)) {
                                        $extractedRange = '#REF!';
                                    }
                                    $excel->addDefinedName(DefinedName::createInstance((string) $definedName['name'], $locatedSheet, $extractedRange, false));
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
                            $chartReader = new Chart($chartNS, $drawingNS);
                            $objChart = $chartReader->readChart($chartElements, basename($chartEntryRef, '.xml'));
                            if (isset($charts[$chartEntryRef])) {
                                $chartPositionRef = $charts[$chartEntryRef]['sheet'] . '!' . $charts[$chartEntryRef]['id'];
                                if (isset($chartDetails[$chartPositionRef]) && $excel->getSheetByName($charts[$chartEntryRef]['sheet']) !== null) {
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
                                        if (array_key_exists('oneCellAnchor', $chartDetails[$chartPositionRef])) {
                                            $objChart->setOneCellAnchor($chartDetails[$chartPositionRef]['oneCellAnchor']);
                                        }
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

    private function parseRichText(?SimpleXMLElement $is): RichText
    {
        $value = new RichText();

        if (isset($is->t)) {
            $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $is->t));
        } elseif ($is !== null) {
            if (is_object($is->r)) {
                foreach ($is->r as $run) {
                    if (!isset($run->rPr)) {
                        $value->createText(StringHelper::controlCharacterOOXML2PHP((string) $run->t));
                    } else {
                        $objText = $value->createTextRun(StringHelper::controlCharacterOOXML2PHP((string) $run->t));
                        $objFont = $objText->getFont() ?? new StyleFont();

                        if (isset($run->rPr->rFont)) {
                            $attr = $run->rPr->rFont->attributes();
                            if (isset($attr['val'])) {
                                $objFont->setName((string) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->sz)) {
                            $attr = $run->rPr->sz->attributes();
                            if (isset($attr['val'])) {
                                $objFont->setSize((float) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->color)) {
                            $objFont->setColor(new Color($this->styleReader->readColor($run->rPr->color)));
                        }
                        if (isset($run->rPr->b)) {
                            $attr = $run->rPr->b->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val']))
                                || (!isset($attr['val']))
                            ) {
                                $objFont->setBold(true);
                            }
                        }
                        if (isset($run->rPr->i)) {
                            $attr = $run->rPr->i->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val']))
                                || (!isset($attr['val']))
                            ) {
                                $objFont->setItalic(true);
                            }
                        }
                        if (isset($run->rPr->vertAlign)) {
                            $attr = $run->rPr->vertAlign->attributes();
                            if (isset($attr['val'])) {
                                $vertAlign = strtolower((string) $attr['val']);
                                if ($vertAlign == 'superscript') {
                                    $objFont->setSuperscript(true);
                                }
                                if ($vertAlign == 'subscript') {
                                    $objFont->setSubscript(true);
                                }
                            }
                        }
                        if (isset($run->rPr->u)) {
                            $attr = $run->rPr->u->attributes();
                            if (!isset($attr['val'])) {
                                $objFont->setUnderline(StyleFont::UNDERLINE_SINGLE);
                            } else {
                                $objFont->setUnderline((string) $attr['val']);
                            }
                        }
                        if (isset($run->rPr->strike)) {
                            $attr = $run->rPr->strike->attributes();
                            if (
                                (isset($attr['val']) && self::boolean((string) $attr['val']))
                                || (!isset($attr['val']))
                            ) {
                                $objFont->setStrikethrough(true);
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
                $this->getSecurityScannerOrThrow()
                    ->scan($dataRels)
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

    private static function getArrayItem(null|array|bool|SimpleXMLElement $array, int|string $key = 0): mixed
    {
        return ($array === null || is_bool($array)) ? null : ($array[$key] ?? null);
    }

    private static function getArrayItemString(null|array|bool|SimpleXMLElement $array, int|string $key = 0): string
    {
        $retVal = self::getArrayItem($array, $key);

        return ($retVal === null || is_scalar($retVal) || $retVal instanceof Stringable) ? ((string) $retVal) : '';
    }

    private static function getArrayItemIntOrSxml(null|array|bool|SimpleXMLElement $array, int|string $key = 0): int|SimpleXMLElement
    {
        $retVal = self::getArrayItem($array, $key);

        return (is_int($retVal) || $retVal instanceof SimpleXMLElement) ? $retVal : 0;
    }

    private static function dirAdd(null|SimpleXMLElement|string $base, null|SimpleXMLElement|string $add): string
    {
        $base = (string) $base;
        $add = (string) $add;

        return (string) preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
    }

    private static function toCSSArray(string $style): array
    {
        $style = self::stripWhiteSpaceFromStyleString($style);

        $temp = explode(';', $style);
        $style = [];
        foreach ($temp as $item) {
            $item = explode(':', $item);

            if (str_contains($item[1], 'px')) {
                $item[1] = str_replace('px', '', $item[1]);
            }
            if (str_contains($item[1], 'pt')) {
                $item[1] = str_replace('pt', '', $item[1]);
                $item[1] = (string) Font::fontSizeToPixels((int) $item[1]);
            }
            if (str_contains($item[1], 'in')) {
                $item[1] = str_replace('in', '', $item[1]);
                $item[1] = (string) Font::inchSizeToPixels((int) $item[1]);
            }
            if (str_contains($item[1], 'cm')) {
                $item[1] = str_replace('cm', '', $item[1]);
                $item[1] = (string) Font::centimeterSizeToPixels((int) $item[1]);
            }

            $style[$item[0]] = $item[1];
        }

        return $style;
    }

    public static function stripWhiteSpaceFromStyleString(string $string): string
    {
        return trim(str_replace(["\r", "\n", ' '], '', $string), ';');
    }

    private static function boolean(string $value): bool
    {
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === 'true' || $value === 'TRUE';
    }

    private function readHyperLinkDrawing(\PhpOffice\PhpSpreadsheet\Worksheet\Drawing $objDrawing, SimpleXMLElement $cellAnchor, array $hyperlinks): void
    {
        $hlinkClick = $cellAnchor->pic->nvPicPr->cNvPr->children(Namespaces::DRAWINGML)->hlinkClick;

        if ($hlinkClick->count() === 0) {
            return;
        }

        $hlinkId = (string) self::getAttributes($hlinkClick, Namespaces::SCHEMA_OFFICE_DOCUMENT)['id'];
        $hyperlink = new Hyperlink(
            $hyperlinks[$hlinkId],
            self::getArrayItemString(self::getAttributes($cellAnchor->pic->nvPicPr->cNvPr), 'name')
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

    private static function getLockValue(SimpleXMLElement $protection, string $key): ?bool
    {
        $returnValue = null;
        $protectKey = $protection[$key];
        if (!empty($protectKey)) {
            $protectKey = (string) $protectKey;
            $returnValue = $protectKey !== 'false' && (bool) $protectKey;
        }

        return $returnValue;
    }

    private function readFormControlProperties(Spreadsheet $excel, string $dir, string $fileWorksheet, Worksheet $docSheet, array &$unparsedLoadedData): void
    {
        $zip = $this->zip;
        if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels') === false) {
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
            $unparsedCtrlProps[$rId]['content'] = $this->getSecurityScannerOrThrow()->scan($this->getFromZipArchive($zip, $unparsedCtrlProps[$rId]['filePath']));
        }
        unset($unparsedCtrlProps);
    }

    private function readPrinterSettings(Spreadsheet $excel, string $dir, string $fileWorksheet, Worksheet $docSheet, array &$unparsedLoadedData): void
    {
        $zip = $this->zip;
        if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels') === false) {
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
            $rId = substr($rId, 3); // rIdXXX
            if (!str_ends_with($rId, 'ps')) {
                $rId = $rId . 'ps'; // rIdXXX, add 'ps' suffix to avoid identical resource identifier collision with unparsed vmlDrawing
            }
            $unparsedPrinterSettings[$rId] = [];
            $target = (string) str_replace('/xl/', '../', (string) $printerSettings['Target']);
            $unparsedPrinterSettings[$rId]['filePath'] = self::dirAdd("$dir/$fileWorksheet", $target);
            $unparsedPrinterSettings[$rId]['relFilePath'] = $target;
            $unparsedPrinterSettings[$rId]['content'] = $this->getSecurityScannerOrThrow()->scan($this->getFromZipArchive($zip, $unparsedPrinterSettings[$rId]['filePath']));
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
                $docSheet->protectCells((string) $protectedRange['sqref'], (string) $protectedRange['password'], true, (string) $protectedRange['name'], (string) $protectedRange['securityDescriptor']);
            }
        }
    }

    private function readAutoFilter(
        SimpleXMLElement $xmlSheet,
        Worksheet $docSheet
    ): void {
        if ($xmlSheet && $xmlSheet->autoFilter) {
            (new AutoFilter($docSheet, $xmlSheet))->load();
        }
    }

    private function readBackgroundImage(
        SimpleXMLElement $xmlSheet,
        Worksheet $docSheet,
        string $relsName
    ): void {
        if ($xmlSheet && $xmlSheet->picture) {
            $id = (string) self::getArrayItemString(self::getAttributes($xmlSheet->picture, Namespaces::SCHEMA_OFFICE_DOCUMENT), 'id');
            $rels = $this->loadZip($relsName);
            foreach ($rels->Relationship as $rel) {
                $attrs = $rel->attributes() ?? [];
                $rid = (string) ($attrs['Id'] ?? '');
                $target = (string) ($attrs['Target'] ?? '');
                if ($rid === $id && substr($target, 0, 2) === '..') {
                    $target = 'xl' . substr($target, 2);
                    $content = $this->getFromZipArchive($this->zip, $target);
                    $docSheet->setBackgroundImage($content);
                }
            }
        }
    }

    private function readTables(
        SimpleXMLElement $xmlSheet,
        Worksheet $docSheet,
        string $dir,
        string $fileWorksheet,
        ZipArchive $zip,
        string $namespaceTable
    ): void {
        if ($xmlSheet && $xmlSheet->tableParts) {
            $attributes = $xmlSheet->tableParts->attributes() ?? ['count' => 0];
            if (((int) $attributes['count']) > 0) {
                $this->readTablesInTablesFile($xmlSheet, $dir, $fileWorksheet, $zip, $docSheet, $namespaceTable);
            }
        }
    }

    private function readTablesInTablesFile(
        SimpleXMLElement $xmlSheet,
        string $dir,
        string $fileWorksheet,
        ZipArchive $zip,
        Worksheet $docSheet,
        string $namespaceTable
    ): void {
        foreach ($xmlSheet->tableParts->tablePart as $tablePart) {
            $relation = self::getAttributes($tablePart, Namespaces::SCHEMA_OFFICE_DOCUMENT);
            $tablePartRel = (string) $relation['id'];
            $relationsFileName = dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels';

            if ($zip->locateName($relationsFileName) !== false) {
                $relsTableReferences = $this->loadZip($relationsFileName, Namespaces::RELATIONSHIPS);
                foreach ($relsTableReferences->Relationship as $relationship) {
                    $relationshipAttributes = self::getAttributes($relationship, '');

                    if ((string) $relationshipAttributes['Id'] === $tablePartRel) {
                        $relationshipFileName = (string) $relationshipAttributes['Target'];
                        $relationshipFilePath = dirname("$dir/$fileWorksheet") . '/' . $relationshipFileName;
                        $relationshipFilePath = File::realpath($relationshipFilePath);

                        if ($this->fileExistsInArchive($this->zip, $relationshipFilePath)) {
                            $tableXml = $this->loadZip($relationshipFilePath, $namespaceTable);
                            (new TableReader($docSheet, $tableXml))->load();
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

        return $array;
    }

    private function processIgnoredErrors(SimpleXMLElement $xml, Worksheet $sheet): void
    {
        $attributes = self::getAttributes($xml);
        $sqref = (string) ($attributes['sqref'] ?? '');
        $numberStoredAsText = (string) ($attributes['numberStoredAsText'] ?? '');
        $formula = (string) ($attributes['formula'] ?? '');
        $twoDigitTextYear = (string) ($attributes['twoDigitTextYear'] ?? '');
        $evalError = (string) ($attributes['evalError'] ?? '');
        if (!empty($sqref)) {
            $explodedSqref = explode(' ', $sqref);
            $pattern1 = '/^([A-Z]{1,3})([0-9]{1,7})(:([A-Z]{1,3})([0-9]{1,7}))?$/';
            foreach ($explodedSqref as $sqref1) {
                if (preg_match($pattern1, $sqref1, $matches) === 1) {
                    $firstRow = $matches[2];
                    $firstCol = $matches[1];
                    if (array_key_exists(3, $matches)) {
                        // https://github.com/phpstan/phpstan/issues/11602
                        $lastCol = $matches[4]; // @phpstan-ignore-line
                        $lastRow = $matches[5]; // @phpstan-ignore-line
                    } else {
                        $lastCol = $firstCol;
                        $lastRow = $firstRow;
                    }
                    ++$lastCol;
                    for ($row = $firstRow; $row <= $lastRow; ++$row) {
                        for ($col = $firstCol; $col !== $lastCol; ++$col) {
                            if ($numberStoredAsText === '1') {
                                $sheet->getCell("$col$row")->getIgnoredErrors()->setNumberStoredAsText(true);
                            }
                            if ($formula === '1') {
                                $sheet->getCell("$col$row")->getIgnoredErrors()->setFormula(true);
                            }
                            if ($twoDigitTextYear === '1') {
                                $sheet->getCell("$col$row")->getIgnoredErrors()->setTwoDigitTextYear(true);
                            }
                            if ($evalError === '1') {
                                $sheet->getCell("$col$row")->getIgnoredErrors()->setEvalError(true);
                            }
                        }
                    }
                }
            }
        }
    }

    private static function storeFormulaAttributes(SimpleXMLElement $f, Worksheet $docSheet, string $r): void
    {
        $formulaAttributes = [];
        $attributes = $f->attributes();
        if (isset($attributes['t'])) {
            $formulaAttributes['t'] = (string) $attributes['t'];
        }
        if (isset($attributes['ref'])) {
            $formulaAttributes['ref'] = (string) $attributes['ref'];
        }
        if (!empty($formulaAttributes)) {
            $docSheet->getCell($r)->setFormulaAttributes($formulaAttributes);
        }
    }

    private static function onlyNoteVml(string $data): bool
    {
        $data = str_replace('<br>', '<br/>', $data);

        try {
            $sxml = @simplexml_load_string($data);
        } catch (Throwable) {
            $sxml = false;
        }

        if ($sxml === false) {
            return false;
        }
        $shapes = $sxml->children(Namespaces::URN_VML);
        foreach ($shapes->shape as $shape) {
            $clientData = $shape->children(Namespaces::URN_EXCEL);
            if (!isset($clientData->ClientData)) {
                return false;
            }
            $attrs = $clientData->ClientData->attributes();
            if (!isset($attrs['ObjectType'])) {
                return false;
            }
            $objectType = (string) $attrs['ObjectType'];
            if ($objectType !== 'Note') {
                return false;
            }
        }

        return true;
    }
}
