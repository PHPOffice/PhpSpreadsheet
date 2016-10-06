<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

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
class Xlsx extends BaseReader implements IReader
{
    /**
     * ReferenceHelper instance
     *
     * @var \PhpOffice\PhpSpreadsheet\ReferenceHelper
     */
    private $referenceHelper = null;

    /**
     * Xlsx\Theme instance
     *
     * @var Xlsx\Theme
     */
    private static $theme = null;

    /**
     * Create a new Xlsx Reader instance
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
        $this->referenceHelper = \PhpOffice\PhpSpreadsheet\ReferenceHelper::getInstance();
    }

    /**
     * Can the current IReader read the file?
     *
     * @param    string         $pFilename
     * @throws   Exception
     * @return   bool
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

        $xl = false;
        // Load file
        $zip = new $zipClass();
        if ($zip->open($pFilename) === true) {
            // check if it is an OOXML archive
            $rels = simplexml_load_string(
                $this->securityScan(
                    $this->getFromZipArchive($zip, '_rels/.rels')
                ),
                'SimpleXMLElement',
                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
            );
            if ($rels !== false) {
                foreach ($rels->Relationship as $rel) {
                    switch ($rel['Type']) {
                        case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument':
                            if (basename($rel['Target']) == 'workbook.xml') {
                                $xl = true;
                            }
                            break;
                    }
                }
            }
            $zip->close();
        }

        return $xl;
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object
     *
     * @param    string         $pFilename
     * @throws   Exception
     */
    public function listWorksheetNames($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        $worksheetNames = [];

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        $zip = new $zipClass();
        $zip->open($pFilename);

        //    The files we're looking at here are small enough that simpleXML is more efficient than XMLReader
        $rels = simplexml_load_string(
            $this->securityScan(
                $this->getFromZipArchive($zip, '_rels/.rels'),
                'SimpleXMLElement',
                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
            )
        ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
        foreach ($rels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument':
                    $xmlWorkbook = simplexml_load_string(
                        $this->securityScan(
                            $this->getFromZipArchive($zip, "{$rel['Target']}"),
                            'SimpleXMLElement',
                            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                        )
                    ); //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"

                    if ($xmlWorkbook->sheets) {
                        foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                            // Check if sheet should be skipped
                            $worksheetNames[] = (string) $eleSheet['name'];
                        }
                    }
            }
        }

        $zip->close();

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
     *
     * @param   string     $pFilename
     * @throws  Exception
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
        $zip->open($pFilename);

        $rels = simplexml_load_string(
            //~ http://schemas.openxmlformats.org/package/2006/relationships"
            $this->securityScan($this->getFromZipArchive($zip, '_rels/.rels')),
            'SimpleXMLElement',
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        foreach ($rels->Relationship as $rel) {
            if ($rel['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument') {
                $dir = dirname($rel['Target']);
                $relsWorkbook = simplexml_load_string(
                    //~ http://schemas.openxmlformats.org/package/2006/relationships"
                    $this->securityScan(
                        $this->getFromZipArchive($zip, "$dir/_rels/" . basename($rel['Target']) . '.rels')
                    ),
                    'SimpleXMLElement',
                    \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                );
                $relsWorkbook->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

                $worksheets = [];
                foreach ($relsWorkbook->Relationship as $ele) {
                    if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet') {
                        $worksheets[(string) $ele['Id']] = $ele['Target'];
                    }
                }

                $xmlWorkbook = simplexml_load_string(
                    //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"
                    $this->securityScan(
                        $this->getFromZipArchive($zip, "{$rel['Target']}")
                    ),
                    'SimpleXMLElement',
                    \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                );
                if ($xmlWorkbook->sheets) {
                    $dir = dirname($rel['Target']);
                    foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                        $tmpInfo = [
                            'worksheetName' => (string) $eleSheet['name'],
                            'lastColumnLetter' => 'A',
                            'lastColumnIndex' => 0,
                            'totalRows' => 0,
                            'totalColumns' => 0,
                        ];

                        $fileWorksheet = $worksheets[(string) self::getArrayItem($eleSheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'), 'id')];

                        $xml = new \XMLReader();
                        $res = $xml->xml(
                            $this->securityScanFile(
                                'zip://' . \PhpOffice\PhpSpreadsheet\Shared\File::realpath($pFilename) . '#' . "$dir/$fileWorksheet"
                            ),
                            null,
                            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                        );
                        $xml->setParserProperty(2, true);

                        $currCells = 0;
                        while ($xml->read()) {
                            if ($xml->name == 'row' && $xml->nodeType == \XMLReader::ELEMENT) {
                                $row = $xml->getAttribute('r');
                                $tmpInfo['totalRows'] = $row;
                                $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                                $currCells = 0;
                            } elseif ($xml->name == 'c' && $xml->nodeType == \XMLReader::ELEMENT) {
                                ++$currCells;
                            }
                        }
                        $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                        $xml->close();

                        $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                        $tmpInfo['lastColumnLetter'] = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);

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
        } else {
            return (bool) $c->v;
        }

        return $value;
    }

    private static function castToError($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private static function castToString($c)
    {
        return isset($c->v) ? (string) $c->v : null;
    }

    private function castToFormula($c, $r, &$cellDataType, &$value, &$calculatedValue, &$sharedFormulas, $castBaseType)
    {
        $cellDataType = 'f';
        $value = "={$c->f}";
        $calculatedValue = self::$castBaseType($c);

        // Shared formula?
        if (isset($c->f['t']) && strtolower((string) $c->f['t']) == 'shared') {
            $instance = (string) $c->f['si'];

            if (!isset($sharedFormulas[(string) $c->f['si']])) {
                $sharedFormulas[$instance] = ['master' => $r, 'formula' => $value];
            } else {
                $master = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($sharedFormulas[$instance]['master']);
                $current = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($r);

                $difference = [0, 0];
                $difference[0] = \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($current[0]) - \PhpOffice\PhpSpreadsheet\Cell::columnIndexFromString($master[0]);
                $difference[1] = $current[1] - $master[1];

                $value = $this->referenceHelper->updateFormulaReferences($sharedFormulas[$instance]['formula'], 'A1', $difference[0], $difference[1]);
            }
        }
    }

    private function getFromZipArchive($archive, $fileName = '')
    {
        // Root-relative paths
        if (strpos($fileName, '//') !== false) {
            $fileName = substr($fileName, strpos($fileName, '//') + 1);
        }
        $fileName = \PhpOffice\PhpSpreadsheet\Shared\File::realpath($fileName);

        // Sadly, some 3rd party xlsx generators don't use consistent case for filenaming
        //    so we need to load case-insensitively from the zip file

        // Apache POI fixes
        $contents = $archive->getFromIndex(
            $archive->locateName($fileName, \ZipArchive::FL_NOCASE)
        );
        if ($contents === false) {
            $contents = $archive->getFromIndex(
                $archive->locateName(substr($fileName, 1), \ZipArchive::FL_NOCASE)
            );
        }

        return $contents;
    }

    /**
     * Loads Spreadsheet from file
     *
     * @param     string         $pFilename
     * @throws    Exception
     * @return    Spreadsheet
     */
    public function load($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }

        // Initialisations
        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $excel->removeSheetByIndex(0);
        if (!$this->readDataOnly) {
            $excel->removeCellStyleXfByIndex(0); // remove the default style
            $excel->removeCellXfByIndex(0); // remove the default style
        }

        $zipClass = \PhpOffice\PhpSpreadsheet\Settings::getZipClass();

        $zip = new $zipClass();
        $zip->open($pFilename);

        //    Read the theme first, because we need the colour scheme when reading the styles
        $wbRels = simplexml_load_string(
            //~ http://schemas.openxmlformats.org/package/2006/relationships"
            $this->securityScan($this->getFromZipArchive($zip, 'xl/_rels/workbook.xml.rels')),
            'SimpleXMLElement',
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        foreach ($wbRels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme':
                    $themeOrderArray = ['lt1', 'dk1', 'lt2', 'dk2'];
                    $themeOrderAdditional = count($themeOrderArray);

                    $xmlTheme = simplexml_load_string(
                        $this->securityScan($this->getFromZipArchive($zip, "xl/{$rel['Target']}")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    if (is_object($xmlTheme)) {
                        $xmlThemeName = $xmlTheme->attributes();
                        $xmlTheme = $xmlTheme->children('http://schemas.openxmlformats.org/drawingml/2006/main');
                        $themeName = (string) $xmlThemeName['name'];

                        $colourScheme = $xmlTheme->themeElements->clrScheme->attributes();
                        $colourSchemeName = (string) $colourScheme['name'];
                        $colourScheme = $xmlTheme->themeElements->clrScheme->children('http://schemas.openxmlformats.org/drawingml/2006/main');

                        $themeColours = [];
                        foreach ($colourScheme as $k => $xmlColour) {
                            $themePos = array_search($k, $themeOrderArray);
                            if ($themePos === false) {
                                $themePos = $themeOrderAdditional++;
                            }
                            if (isset($xmlColour->sysClr)) {
                                $xmlColourData = $xmlColour->sysClr->attributes();
                                $themeColours[$themePos] = $xmlColourData['lastClr'];
                            } elseif (isset($xmlColour->srgbClr)) {
                                $xmlColourData = $xmlColour->srgbClr->attributes();
                                $themeColours[$themePos] = $xmlColourData['val'];
                            }
                        }
                        self::$theme = new Xlsx\Theme($themeName, $colourSchemeName, $themeColours);
                    }
                    break;
            }
        }

        $rels = simplexml_load_string(
            //~ http://schemas.openxmlformats.org/package/2006/relationships"
            $this->securityScan($this->getFromZipArchive($zip, '_rels/.rels')),
            'SimpleXMLElement',
            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
        );
        foreach ($rels->Relationship as $rel) {
            switch ($rel['Type']) {
                case 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties':
                    $xmlCore = simplexml_load_string(
                        $this->securityScan($this->getFromZipArchive($zip, "{$rel['Target']}")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    if (is_object($xmlCore)) {
                        $xmlCore->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
                        $xmlCore->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
                        $xmlCore->registerXPathNamespace('cp', 'http://schemas.openxmlformats.org/package/2006/metadata/core-properties');
                        $docProps = $excel->getProperties();
                        $docProps->setCreator((string) self::getArrayItem($xmlCore->xpath('dc:creator')));
                        $docProps->setLastModifiedBy((string) self::getArrayItem($xmlCore->xpath('cp:lastModifiedBy')));
                        $docProps->setCreated(strtotime(self::getArrayItem($xmlCore->xpath('dcterms:created')))); //! respect xsi:type
                        $docProps->setModified(strtotime(self::getArrayItem($xmlCore->xpath('dcterms:modified')))); //! respect xsi:type
                        $docProps->setTitle((string) self::getArrayItem($xmlCore->xpath('dc:title')));
                        $docProps->setDescription((string) self::getArrayItem($xmlCore->xpath('dc:description')));
                        $docProps->setSubject((string) self::getArrayItem($xmlCore->xpath('dc:subject')));
                        $docProps->setKeywords((string) self::getArrayItem($xmlCore->xpath('cp:keywords')));
                        $docProps->setCategory((string) self::getArrayItem($xmlCore->xpath('cp:category')));
                    }
                    break;
                case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties':
                    $xmlCore = simplexml_load_string(
                        $this->securityScan($this->getFromZipArchive($zip, "{$rel['Target']}")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    if (is_object($xmlCore)) {
                        $docProps = $excel->getProperties();
                        if (isset($xmlCore->Company)) {
                            $docProps->setCompany((string) $xmlCore->Company);
                        }
                        if (isset($xmlCore->Manager)) {
                            $docProps->setManager((string) $xmlCore->Manager);
                        }
                    }
                    break;
                case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties':
                    $xmlCore = simplexml_load_string(
                        $this->securityScan($this->getFromZipArchive($zip, "{$rel['Target']}")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    if (is_object($xmlCore)) {
                        $docProps = $excel->getProperties();
                        foreach ($xmlCore as $xmlProperty) {
                            $cellDataOfficeAttributes = $xmlProperty->attributes();
                            if (isset($cellDataOfficeAttributes['name'])) {
                                $propertyName = (string) $cellDataOfficeAttributes['name'];
                                $cellDataOfficeChildren = $xmlProperty->children('http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');
                                $attributeType = $cellDataOfficeChildren->getName();
                                $attributeValue = (string) $cellDataOfficeChildren->{$attributeType};
                                $attributeValue = \PhpOffice\PhpSpreadsheet\Document\Properties::convertProperty($attributeValue, $attributeType);
                                $attributeType = \PhpOffice\PhpSpreadsheet\Document\Properties::convertPropertyType($attributeType);
                                $docProps->setCustomProperty($propertyName, $attributeValue, $attributeType);
                            }
                        }
                    }
                    break;
                //Ribbon
                case 'http://schemas.microsoft.com/office/2006/relationships/ui/extensibility':
                    $customUI = $rel['Target'];
                    if (!is_null($customUI)) {
                        $this->readRibbon($excel, $customUI, $zip);
                    }
                    break;
                case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument':
                    $dir = dirname($rel['Target']);
                    $relsWorkbook = simplexml_load_string(
                        //~ http://schemas.openxmlformats.org/package/2006/relationships"
                        $this->securityScan($this->getFromZipArchive($zip, "$dir/_rels/" . basename($rel['Target']) . '.rels')),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    $relsWorkbook->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

                    $sharedStrings = [];
                    $xpath = self::getArrayItem($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings']"));
                    $xmlStrings = simplexml_load_string(
                        //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"
                        $this->securityScan($this->getFromZipArchive($zip, "$dir/$xpath[Target]")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    if (isset($xmlStrings) && isset($xmlStrings->si)) {
                        foreach ($xmlStrings->si as $val) {
                            if (isset($val->t)) {
                                $sharedStrings[] = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::controlCharacterOOXML2PHP((string) $val->t);
                            } elseif (isset($val->r)) {
                                $sharedStrings[] = $this->parseRichText($val);
                            }
                        }
                    }

                    $worksheets = [];
                    $macros = $customUI = null;
                    foreach ($relsWorkbook->Relationship as $ele) {
                        switch ($ele['Type']) {
                            case 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet':
                                $worksheets[(string) $ele['Id']] = $ele['Target'];
                                break;
                            // a vbaProject ? (: some macros)
                            case 'http://schemas.microsoft.com/office/2006/relationships/vbaProject':
                                $macros = $ele['Target'];
                                break;
                        }
                    }

                    if (!is_null($macros)) {
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
                    $styles = [];
                    $cellStyles = [];
                    $xpath = self::getArrayItem($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles']"));
                    $xmlStyles = simplexml_load_string(
                        //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"
                        $this->securityScan($this->getFromZipArchive($zip, "$dir/$xpath[Target]")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );
                    $numFmts = null;
                    if ($xmlStyles && $xmlStyles->numFmts[0]) {
                        $numFmts = $xmlStyles->numFmts[0];
                    }
                    if (isset($numFmts) && ($numFmts !== null)) {
                        $numFmts->registerXPathNamespace('sml', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    }
                    if (!$this->readDataOnly && $xmlStyles) {
                        foreach ($xmlStyles->cellXfs->xf as $xf) {
                            $numFmt = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL;

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
                                if ((int) $xf['numFmtId'] < 164 &&
                                    \PhpOffice\PhpSpreadsheet\Style\NumberFormat::builtInFormatCode((int) $xf['numFmtId']) !== '') {
                                    $numFmt = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }
                            $quotePrefix = false;
                            if (isset($xf['quotePrefix'])) {
                                $quotePrefix = (boolean) $xf['quotePrefix'];
                            }

                            $style = (object) [
                                'numFmt' => $numFmt,
                                'font' => $xmlStyles->fonts->font[intval($xf['fontId'])],
                                'fill' => $xmlStyles->fills->fill[intval($xf['fillId'])],
                                'border' => $xmlStyles->borders->border[intval($xf['borderId'])],
                                'alignment' => $xf->alignment,
                                'protection' => $xf->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $styles[] = $style;

                            // add style to cellXf collection
                            $objStyle = new \PhpOffice\PhpSpreadsheet\Style();
                            self::readStyle($objStyle, $style);
                            $excel->addCellXf($objStyle);
                        }

                        foreach ($xmlStyles->cellStyleXfs->xf as $xf) {
                            $numFmt = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL;
                            if ($numFmts && $xf['numFmtId']) {
                                $tmpNumFmt = self::getArrayItem($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]"));
                                if (isset($tmpNumFmt['formatCode'])) {
                                    $numFmt = (string) $tmpNumFmt['formatCode'];
                                } elseif ((int) $xf['numFmtId'] < 165) {
                                    $numFmt = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::builtInFormatCode((int) $xf['numFmtId']);
                                }
                            }

                            $cellStyle = (object) [
                                'numFmt' => $numFmt,
                                'font' => $xmlStyles->fonts->font[intval($xf['fontId'])],
                                'fill' => $xmlStyles->fills->fill[intval($xf['fillId'])],
                                'border' => $xmlStyles->borders->border[intval($xf['borderId'])],
                                'alignment' => $xf->alignment,
                                'protection' => $xf->protection,
                                'quotePrefix' => $quotePrefix,
                            ];
                            $cellStyles[] = $cellStyle;

                            // add style to cellStyleXf collection
                            $objStyle = new \PhpOffice\PhpSpreadsheet\Style();
                            self::readStyle($objStyle, $cellStyle);
                            $excel->addCellStyleXf($objStyle);
                        }
                    }

                    $dxfs = [];
                    if (!$this->readDataOnly && $xmlStyles) {
                        //    Conditional Styles
                        if ($xmlStyles->dxfs) {
                            foreach ($xmlStyles->dxfs->dxf as $dxf) {
                                $style = new \PhpOffice\PhpSpreadsheet\Style(false, true);
                                self::readStyle($style, $dxf);
                                $dxfs[] = $style;
                            }
                        }
                        //    Cell Styles
                        if ($xmlStyles->cellStyles) {
                            foreach ($xmlStyles->cellStyles->cellStyle as $cellStyle) {
                                if (intval($cellStyle['builtinId']) == 0) {
                                    if (isset($cellStyles[intval($cellStyle['xfId'])])) {
                                        // Set default style
                                        $style = new \PhpOffice\PhpSpreadsheet\Style();
                                        self::readStyle($style, $cellStyles[intval($cellStyle['xfId'])]);

                                        // normal style, currently not using it for anything
                                    }
                                }
                            }
                        }
                    }

                    $xmlWorkbook = simplexml_load_string(
                        //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"
                        $this->securityScan($this->getFromZipArchive($zip, "{$rel['Target']}")),
                        'SimpleXMLElement',
                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                    );

                    // Set base date
                    if ($xmlWorkbook->workbookPr) {
                        \PhpOffice\PhpSpreadsheet\Shared\Date::setExcelCalendar(\PhpOffice\PhpSpreadsheet\Shared\Date::CALENDAR_WINDOWS_1900);
                        if (isset($xmlWorkbook->workbookPr['date1904'])) {
                            if (self::boolean((string) $xmlWorkbook->workbookPr['date1904'])) {
                                \PhpOffice\PhpSpreadsheet\Shared\Date::setExcelCalendar(\PhpOffice\PhpSpreadsheet\Shared\Date::CALENDAR_MAC_1904);
                            }
                        }
                    }

                    $sheetId = 0; // keep track of new sheet id in final workbook
                    $oldSheetId = -1; // keep track of old sheet id in final workbook
                    $countSkippedSheets = 0; // keep track of number of skipped sheets
                    $mapSheetId = []; // mapping of sheet ids from old to new

                    $charts = $chartDetails = [];

                    if ($xmlWorkbook->sheets) {
                        foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
                            ++$oldSheetId;

                            // Check if sheet should be skipped
                            if (isset($this->loadSheetsOnly) && !in_array((string) $eleSheet['name'], $this->loadSheetsOnly)) {
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
                            $docSheet->setTitle((string) $eleSheet['name'], false);
                            $fileWorksheet = $worksheets[(string) self::getArrayItem($eleSheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'), 'id')];
                            $xmlSheet = simplexml_load_string(
                                //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main"
                                $this->securityScan($this->getFromZipArchive($zip, "$dir/$fileWorksheet")),
                                'SimpleXMLElement',
                                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                            );

                            $sharedFormulas = [];

                            if (isset($eleSheet['state']) && (string) $eleSheet['state'] != '') {
                                $docSheet->setSheetState((string) $eleSheet['state']);
                            }

                            if (isset($xmlSheet->sheetViews) && isset($xmlSheet->sheetViews->sheetView)) {
                                if (isset($xmlSheet->sheetViews->sheetView['zoomScale'])) {
                                    $docSheet->getSheetView()->setZoomScale(intval($xmlSheet->sheetViews->sheetView['zoomScale']));
                                }
                                if (isset($xmlSheet->sheetViews->sheetView['zoomScaleNormal'])) {
                                    $docSheet->getSheetView()->setZoomScaleNormal(intval($xmlSheet->sheetViews->sheetView['zoomScaleNormal']));
                                }
                                if (isset($xmlSheet->sheetViews->sheetView['view'])) {
                                    $docSheet->getSheetView()->setView((string) $xmlSheet->sheetViews->sheetView['view']);
                                }
                                if (isset($xmlSheet->sheetViews->sheetView['showGridLines'])) {
                                    $docSheet->setShowGridLines(self::boolean((string) $xmlSheet->sheetViews->sheetView['showGridLines']));
                                }
                                if (isset($xmlSheet->sheetViews->sheetView['showRowColHeaders'])) {
                                    $docSheet->setShowRowColHeaders(self::boolean((string) $xmlSheet->sheetViews->sheetView['showRowColHeaders']));
                                }
                                if (isset($xmlSheet->sheetViews->sheetView['rightToLeft'])) {
                                    $docSheet->setRightToLeft(self::boolean((string) $xmlSheet->sheetViews->sheetView['rightToLeft']));
                                }
                                if (isset($xmlSheet->sheetViews->sheetView->pane)) {
                                    if (isset($xmlSheet->sheetViews->sheetView->pane['topLeftCell'])) {
                                        $docSheet->freezePane((string) $xmlSheet->sheetViews->sheetView->pane['topLeftCell']);
                                    } else {
                                        $xSplit = 0;
                                        $ySplit = 0;

                                        if (isset($xmlSheet->sheetViews->sheetView->pane['xSplit'])) {
                                            $xSplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['xSplit']);
                                        }

                                        if (isset($xmlSheet->sheetViews->sheetView->pane['ySplit'])) {
                                            $ySplit = 1 + intval($xmlSheet->sheetViews->sheetView->pane['ySplit']);
                                        }

                                        $docSheet->freezePaneByColumnAndRow($xSplit, $ySplit);
                                    }
                                }

                                if (isset($xmlSheet->sheetViews->sheetView->selection)) {
                                    if (isset($xmlSheet->sheetViews->sheetView->selection['sqref'])) {
                                        $sqref = (string) $xmlSheet->sheetViews->sheetView->selection['sqref'];
                                        $sqref = explode(' ', $sqref);
                                        $sqref = $sqref[0];
                                        $docSheet->setSelectedCells($sqref);
                                    }
                                }
                            }

                            if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->tabColor)) {
                                if (isset($xmlSheet->sheetPr->tabColor['rgb'])) {
                                    $docSheet->getTabColor()->setARGB((string) $xmlSheet->sheetPr->tabColor['rgb']);
                                }
                            }
                            if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr['codeName'])) {
                                $docSheet->setCodeName((string) $xmlSheet->sheetPr['codeName']);
                            }
                            if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->outlinePr)) {
                                if (isset($xmlSheet->sheetPr->outlinePr['summaryRight']) &&
                                    !self::boolean((string) $xmlSheet->sheetPr->outlinePr['summaryRight'])) {
                                    $docSheet->setShowSummaryRight(false);
                                } else {
                                    $docSheet->setShowSummaryRight(true);
                                }

                                if (isset($xmlSheet->sheetPr->outlinePr['summaryBelow']) &&
                                    !self::boolean((string) $xmlSheet->sheetPr->outlinePr['summaryBelow'])) {
                                    $docSheet->setShowSummaryBelow(false);
                                } else {
                                    $docSheet->setShowSummaryBelow(true);
                                }
                            }

                            if (isset($xmlSheet->sheetPr) && isset($xmlSheet->sheetPr->pageSetUpPr)) {
                                if (isset($xmlSheet->sheetPr->pageSetUpPr['fitToPage']) &&
                                    !self::boolean((string) $xmlSheet->sheetPr->pageSetUpPr['fitToPage'])) {
                                    $docSheet->getPageSetup()->setFitToPage(false);
                                } else {
                                    $docSheet->getPageSetup()->setFitToPage(true);
                                }
                            }

                            if (isset($xmlSheet->sheetFormatPr)) {
                                if (isset($xmlSheet->sheetFormatPr['customHeight']) &&
                                    self::boolean((string) $xmlSheet->sheetFormatPr['customHeight']) &&
                                    isset($xmlSheet->sheetFormatPr['defaultRowHeight'])) {
                                    $docSheet->getDefaultRowDimension()->setRowHeight((float) $xmlSheet->sheetFormatPr['defaultRowHeight']);
                                }
                                if (isset($xmlSheet->sheetFormatPr['defaultColWidth'])) {
                                    $docSheet->getDefaultColumnDimension()->setWidth((float) $xmlSheet->sheetFormatPr['defaultColWidth']);
                                }
                                if (isset($xmlSheet->sheetFormatPr['zeroHeight']) &&
                                    ((string) $xmlSheet->sheetFormatPr['zeroHeight'] == '1')) {
                                    $docSheet->getDefaultRowDimension()->setZeroHeight(true);
                                }
                            }

                            if (isset($xmlSheet->cols) && !$this->readDataOnly) {
                                foreach ($xmlSheet->cols->col as $col) {
                                    for ($i = intval($col['min']) - 1; $i < intval($col['max']); ++$i) {
                                        if ($col['style'] && !$this->readDataOnly) {
                                            $docSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($i))->setXfIndex(intval($col['style']));
                                        }
                                        if (self::boolean($col['hidden'])) {
                                            $docSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($i))->setVisible(false);
                                        }
                                        if (self::boolean($col['collapsed'])) {
                                            $docSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($i))->setCollapsed(true);
                                        }
                                        if ($col['outlineLevel'] > 0) {
                                            $docSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($i))->setOutlineLevel(intval($col['outlineLevel']));
                                        }
                                        $docSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($i))->setWidth(floatval($col['width']));

                                        if (intval($col['max']) == 16384) {
                                            break;
                                        }
                                    }
                                }
                            }

                            if (isset($xmlSheet->printOptions) && !$this->readDataOnly) {
                                if (self::boolean((string) $xmlSheet->printOptions['gridLinesSet'])) {
                                    $docSheet->setShowGridlines(true);
                                }
                                if (self::boolean((string) $xmlSheet->printOptions['gridLines'])) {
                                    $docSheet->setPrintGridlines(true);
                                }
                                if (self::boolean((string) $xmlSheet->printOptions['horizontalCentered'])) {
                                    $docSheet->getPageSetup()->setHorizontalCentered(true);
                                }
                                if (self::boolean((string) $xmlSheet->printOptions['verticalCentered'])) {
                                    $docSheet->getPageSetup()->setVerticalCentered(true);
                                }
                            }

                            if ($xmlSheet && $xmlSheet->sheetData && $xmlSheet->sheetData->row) {
                                foreach ($xmlSheet->sheetData->row as $row) {
                                    if ($row['ht'] && !$this->readDataOnly) {
                                        $docSheet->getRowDimension(intval($row['r']))->setRowHeight(floatval($row['ht']));
                                    }
                                    if (self::boolean($row['hidden']) && !$this->readDataOnly) {
                                        $docSheet->getRowDimension(intval($row['r']))->setVisible(false);
                                    }
                                    if (self::boolean($row['collapsed'])) {
                                        $docSheet->getRowDimension(intval($row['r']))->setCollapsed(true);
                                    }
                                    if ($row['outlineLevel'] > 0) {
                                        $docSheet->getRowDimension(intval($row['r']))->setOutlineLevel(intval($row['outlineLevel']));
                                    }
                                    if ($row['s'] && !$this->readDataOnly) {
                                        $docSheet->getRowDimension(intval($row['r']))->setXfIndex(intval($row['s']));
                                    }

                                    foreach ($row->c as $c) {
                                        $r = (string) $c['r'];
                                        $cellDataType = (string) $c['t'];
                                        $value = null;
                                        $calculatedValue = null;

                                        // Read cell?
                                        if ($this->getReadFilter() !== null) {
                                            $coordinates = \PhpOffice\PhpSpreadsheet\Cell::coordinateFromString($r);

                                            if (!$this->getReadFilter()->readCell($coordinates[0], $coordinates[1], $docSheet->getTitle())) {
                                                continue;
                                            }
                                        }

                                        // Read cell!
                                        switch ($cellDataType) {
                                            case 's':
                                                if ((string) $c->v != '') {
                                                    $value = $sharedStrings[intval($c->v)];

                                                    if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText) {
                                                        $value = clone $value;
                                                    }
                                                } else {
                                                    $value = '';
                                                }
                                                break;
                                            case 'b':
                                                if (!isset($c->f)) {
                                                    $value = self::castToBoolean($c);
                                                } else {
                                                    // Formula
                                                    $this->castToFormula($c, $r, $cellDataType, $value, $calculatedValue, $sharedFormulas, 'castToBoolean');
                                                    if (isset($c->f['t'])) {
                                                        $att = [];
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
                                                }
                                                break;
                                        }

                                        // Check for numeric values
                                        if (is_numeric($value) && $cellDataType != 's') {
                                            if ($value == (int) $value) {
                                                $value = (int) $value;
                                            } elseif ($value == (float) $value) {
                                                $value = (float) $value;
                                            } elseif ($value == (double) $value) {
                                                $value = (double) $value;
                                            }
                                        }

                                        // Rich text?
                                        if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText && $this->readDataOnly) {
                                            $value = $value->getPlainText();
                                        }

                                        $cell = $docSheet->getCell($r);
                                        // Assign value
                                        if ($cellDataType != '') {
                                            $cell->setValueExplicit($value, $cellDataType);
                                        } else {
                                            $cell->setValue($value);
                                        }
                                        if ($calculatedValue !== null) {
                                            $cell->setCalculatedValue($calculatedValue);
                                        }

                                        // Style information?
                                        if ($c['s'] && !$this->readDataOnly) {
                                            // no style index means 0, it seems
                                            $cell->setXfIndex(isset($styles[intval($c['s'])]) ?
                                                intval($c['s']) : 0);
                                        }
                                    }
                                }
                            }

                            $conditionals = [];
                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->conditionalFormatting) {
                                foreach ($xmlSheet->conditionalFormatting as $conditional) {
                                    foreach ($conditional->cfRule as $cfRule) {
                                        if (((string) $cfRule['type'] == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_NONE || (string) $cfRule['type'] == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS || (string) $cfRule['type'] == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT || (string) $cfRule['type'] == \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION) && isset($dxfs[intval($cfRule['dxfId'])])) {
                                            $conditionals[(string) $conditional['sqref']][intval($cfRule['priority'])] = $cfRule;
                                        }
                                    }
                                }

                                foreach ($conditionals as $ref => $cfRules) {
                                    ksort($cfRules);
                                    $conditionalStyles = [];
                                    foreach ($cfRules as $cfRule) {
                                        $objConditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                                        $objConditional->setConditionType((string) $cfRule['type']);
                                        $objConditional->setOperatorType((string) $cfRule['operator']);

                                        if ((string) $cfRule['text'] != '') {
                                            $objConditional->setText((string) $cfRule['text']);
                                        }

                                        if (count($cfRule->formula) > 1) {
                                            foreach ($cfRule->formula as $formula) {
                                                $objConditional->addCondition((string) $formula);
                                            }
                                        } else {
                                            $objConditional->addCondition((string) $cfRule->formula);
                                        }
                                        $objConditional->setStyle(clone $dxfs[intval($cfRule['dxfId'])]);
                                        $conditionalStyles[] = $objConditional;
                                    }

                                    // Extract all cell references in $ref
                                    $cellBlocks = explode(' ', str_replace('$', '', strtoupper($ref)));
                                    foreach ($cellBlocks as $cellBlock) {
                                        $docSheet->getStyle($cellBlock)->setConditionalStyles($conditionalStyles);
                                    }
                                }
                            }

                            $aKeys = ['sheet', 'objects', 'scenarios', 'formatCells', 'formatColumns', 'formatRows', 'insertColumns', 'insertRows', 'insertHyperlinks', 'deleteColumns', 'deleteRows', 'selectLockedCells', 'sort', 'autoFilter', 'pivotTables', 'selectUnlockedCells'];
                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
                                foreach ($aKeys as $key) {
                                    $method = 'set' . ucfirst($key);
                                    $docSheet->getProtection()->$method(self::boolean((string) $xmlSheet->sheetProtection[$key]));
                                }
                            }

                            if (!$this->readDataOnly && $xmlSheet && $xmlSheet->sheetProtection) {
                                $docSheet->getProtection()->setPassword((string) $xmlSheet->sheetProtection['password'], true);
                                if ($xmlSheet->protectedRanges->protectedRange) {
                                    foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
                                        $docSheet->protectCells((string) $protectedRange['sqref'], (string) $protectedRange['password'], true);
                                    }
                                }
                            }

                            if ($xmlSheet && $xmlSheet->autoFilter && !$this->readDataOnly) {
                                $autoFilterRange = (string) $xmlSheet->autoFilter['ref'];
                                if (strpos($autoFilterRange, ':') !== false) {
                                    $autoFilter = $docSheet->getAutoFilter();
                                    $autoFilter->setRange($autoFilterRange);

                                    foreach ($xmlSheet->autoFilter->filterColumn as $filterColumn) {
                                        $column = $autoFilter->getColumnByOffset((integer) $filterColumn['colId']);
                                        //    Check for standard filters
                                        if ($filterColumn->filters) {
                                            $column->setFilterType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER);
                                            $filters = $filterColumn->filters;
                                            if ((isset($filters['blank'])) && ($filters['blank'] == 1)) {
                                                //    Operator is undefined, but always treated as EQUAL
                                                $column->createRule()->setRule(null, '')->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_FILTER);
                                            }
                                            //    Standard filters are always an OR join, so no join rule needs to be set
                                            //    Entries can be either filter elements
                                            foreach ($filters->filter as $filterRule) {
                                                //    Operator is undefined, but always treated as EQUAL
                                                $column->createRule()->setRule(null, (string) $filterRule['val'])->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_FILTER);
                                            }
                                            //    Or Date Group elements
                                            foreach ($filters->dateGroupItem as $dateGroupItem) {
                                                $column->createRule()->setRule(
                                                    //    Operator is undefined, but always treated as EQUAL
                                                    null,
                                                    [
                                                        'year' => (string) $dateGroupItem['year'],
                                                        'month' => (string) $dateGroupItem['month'],
                                                        'day' => (string) $dateGroupItem['day'],
                                                        'hour' => (string) $dateGroupItem['hour'],
                                                        'minute' => (string) $dateGroupItem['minute'],
                                                        'second' => (string) $dateGroupItem['second'],
                                                    ],
                                                    (string) $dateGroupItem['dateTimeGrouping']
                                                )
                                                ->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP);
                                            }
                                        }
                                        //    Check for custom filters
                                        if ($filterColumn->customFilters) {
                                            $column->setFilterType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
                                            $customFilters = $filterColumn->customFilters;
                                            //    Custom filters can an AND or an OR join;
                                            //        and there should only ever be one or two entries
                                            if ((isset($customFilters['and'])) && ($customFilters['and'] == 1)) {
                                                $column->setJoin(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND);
                                            }
                                            foreach ($customFilters->customFilter as $filterRule) {
                                                $column->createRule()->setRule(
                                                    (string) $filterRule['operator'],
                                                    (string) $filterRule['val']
                                                )
                                                ->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
                                            }
                                        }
                                        //    Check for dynamic filters
                                        if ($filterColumn->dynamicFilter) {
                                            $column->setFilterType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
                                            //    We should only ever have one dynamic filter
                                            foreach ($filterColumn->dynamicFilter as $filterRule) {
                                                $column->createRule()->setRule(
                                                    //    Operator is undefined, but always treated as EQUAL
                                                    null,
                                                    (string) $filterRule['val'],
                                                    (string) $filterRule['type']
                                                )
                                                ->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
                                                if (isset($filterRule['val'])) {
                                                    $column->setAttribute('val', (string) $filterRule['val']);
                                                }
                                                if (isset($filterRule['maxVal'])) {
                                                    $column->setAttribute('maxVal', (string) $filterRule['maxVal']);
                                                }
                                            }
                                        }
                                        //    Check for dynamic filters
                                        if ($filterColumn->top10) {
                                            $column->setFilterType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
                                            //    We should only ever have one top10 filter
                                            foreach ($filterColumn->top10 as $filterRule) {
                                                $column->createRule()->setRule(
                                                    (((isset($filterRule['percent'])) && ($filterRule['percent'] == 1))
                                                        ? \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT
                                                        : \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE
                                                    ),
                                                    (string) $filterRule['val'],
                                                    (((isset($filterRule['top'])) && ($filterRule['top'] == 1))
                                                        ? \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP
                                                        : \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM
                                                    )
                                                )
                                                ->setRuleType(\PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);
                                            }
                                        }
                                    }
                                }
                            }

                            if ($xmlSheet && $xmlSheet->mergeCells && $xmlSheet->mergeCells->mergeCell && !$this->readDataOnly) {
                                foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
                                    $mergeRef = (string) $mergeCell['ref'];
                                    if (strpos($mergeRef, ':') !== false) {
                                        $docSheet->mergeCells((string) $mergeCell['ref']);
                                    }
                                }
                            }

                            if ($xmlSheet && $xmlSheet->pageMargins && !$this->readDataOnly) {
                                $docPageMargins = $docSheet->getPageMargins();
                                $docPageMargins->setLeft(floatval($xmlSheet->pageMargins['left']));
                                $docPageMargins->setRight(floatval($xmlSheet->pageMargins['right']));
                                $docPageMargins->setTop(floatval($xmlSheet->pageMargins['top']));
                                $docPageMargins->setBottom(floatval($xmlSheet->pageMargins['bottom']));
                                $docPageMargins->setHeader(floatval($xmlSheet->pageMargins['header']));
                                $docPageMargins->setFooter(floatval($xmlSheet->pageMargins['footer']));
                            }

                            if ($xmlSheet && $xmlSheet->pageSetup && !$this->readDataOnly) {
                                $docPageSetup = $docSheet->getPageSetup();

                                if (isset($xmlSheet->pageSetup['orientation'])) {
                                    $docPageSetup->setOrientation((string) $xmlSheet->pageSetup['orientation']);
                                }
                                if (isset($xmlSheet->pageSetup['paperSize'])) {
                                    $docPageSetup->setPaperSize(intval($xmlSheet->pageSetup['paperSize']));
                                }
                                if (isset($xmlSheet->pageSetup['scale'])) {
                                    $docPageSetup->setScale(intval($xmlSheet->pageSetup['scale']), false);
                                }
                                if (isset($xmlSheet->pageSetup['fitToHeight']) && intval($xmlSheet->pageSetup['fitToHeight']) >= 0) {
                                    $docPageSetup->setFitToHeight(intval($xmlSheet->pageSetup['fitToHeight']), false);
                                }
                                if (isset($xmlSheet->pageSetup['fitToWidth']) && intval($xmlSheet->pageSetup['fitToWidth']) >= 0) {
                                    $docPageSetup->setFitToWidth(intval($xmlSheet->pageSetup['fitToWidth']), false);
                                }
                                if (isset($xmlSheet->pageSetup['firstPageNumber']) && isset($xmlSheet->pageSetup['useFirstPageNumber']) &&
                                    self::boolean((string) $xmlSheet->pageSetup['useFirstPageNumber'])) {
                                    $docPageSetup->setFirstPageNumber(intval($xmlSheet->pageSetup['firstPageNumber']));
                                }
                            }

                            if ($xmlSheet && $xmlSheet->headerFooter && !$this->readDataOnly) {
                                $docHeaderFooter = $docSheet->getHeaderFooter();

                                if (isset($xmlSheet->headerFooter['differentOddEven']) &&
                                    self::boolean((string) $xmlSheet->headerFooter['differentOddEven'])) {
                                    $docHeaderFooter->setDifferentOddEven(true);
                                } else {
                                    $docHeaderFooter->setDifferentOddEven(false);
                                }
                                if (isset($xmlSheet->headerFooter['differentFirst']) &&
                                    self::boolean((string) $xmlSheet->headerFooter['differentFirst'])) {
                                    $docHeaderFooter->setDifferentFirst(true);
                                } else {
                                    $docHeaderFooter->setDifferentFirst(false);
                                }
                                if (isset($xmlSheet->headerFooter['scaleWithDoc']) &&
                                    !self::boolean((string) $xmlSheet->headerFooter['scaleWithDoc'])) {
                                    $docHeaderFooter->setScaleWithDocument(false);
                                } else {
                                    $docHeaderFooter->setScaleWithDocument(true);
                                }
                                if (isset($xmlSheet->headerFooter['alignWithMargins']) &&
                                    !self::boolean((string) $xmlSheet->headerFooter['alignWithMargins'])) {
                                    $docHeaderFooter->setAlignWithMargins(false);
                                } else {
                                    $docHeaderFooter->setAlignWithMargins(true);
                                }

                                $docHeaderFooter->setOddHeader((string) $xmlSheet->headerFooter->oddHeader);
                                $docHeaderFooter->setOddFooter((string) $xmlSheet->headerFooter->oddFooter);
                                $docHeaderFooter->setEvenHeader((string) $xmlSheet->headerFooter->evenHeader);
                                $docHeaderFooter->setEvenFooter((string) $xmlSheet->headerFooter->evenFooter);
                                $docHeaderFooter->setFirstHeader((string) $xmlSheet->headerFooter->firstHeader);
                                $docHeaderFooter->setFirstFooter((string) $xmlSheet->headerFooter->firstFooter);
                            }

                            if ($xmlSheet && $xmlSheet->rowBreaks && $xmlSheet->rowBreaks->brk && !$this->readDataOnly) {
                                foreach ($xmlSheet->rowBreaks->brk as $brk) {
                                    if ($brk['man']) {
                                        $docSheet->setBreak("A$brk[id]", \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_ROW);
                                    }
                                }
                            }
                            if ($xmlSheet && $xmlSheet->colBreaks && $xmlSheet->colBreaks->brk && !$this->readDataOnly) {
                                foreach ($xmlSheet->colBreaks->brk as $brk) {
                                    if ($brk['man']) {
                                        $docSheet->setBreak(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $brk['id']) . '1', \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_COLUMN);
                                    }
                                }
                            }

                            if ($xmlSheet && $xmlSheet->dataValidations && !$this->readDataOnly) {
                                foreach ($xmlSheet->dataValidations->dataValidation as $dataValidation) {
                                    // Uppercase coordinate
                                    $range = strtoupper($dataValidation['sqref']);
                                    $rangeSet = explode(' ', $range);
                                    foreach ($rangeSet as $range) {
                                        $stRange = $docSheet->shrinkRangeToFit($range);

                                        // Extract all cell references in $range
                                        foreach (\PhpOffice\PhpSpreadsheet\Cell::extractAllCellReferencesInRange($stRange) as $reference) {
                                            // Create validation
                                            $docValidation = $docSheet->getCell($reference)->getDataValidation();
                                            $docValidation->setType((string) $dataValidation['type']);
                                            $docValidation->setErrorStyle((string) $dataValidation['errorStyle']);
                                            $docValidation->setOperator((string) $dataValidation['operator']);
                                            $docValidation->setAllowBlank($dataValidation['allowBlank'] != 0);
                                            $docValidation->setShowDropDown($dataValidation['showDropDown'] == 0);
                                            $docValidation->setShowInputMessage($dataValidation['showInputMessage'] != 0);
                                            $docValidation->setShowErrorMessage($dataValidation['showErrorMessage'] != 0);
                                            $docValidation->setErrorTitle((string) $dataValidation['errorTitle']);
                                            $docValidation->setError((string) $dataValidation['error']);
                                            $docValidation->setPromptTitle((string) $dataValidation['promptTitle']);
                                            $docValidation->setPrompt((string) $dataValidation['prompt']);
                                            $docValidation->setFormula1((string) $dataValidation->formula1);
                                            $docValidation->setFormula2((string) $dataValidation->formula2);
                                        }
                                    }
                                }
                            }

                            // Add hyperlinks
                            $hyperlinks = [];
                            if (!$this->readDataOnly) {
                                // Locate hyperlink relations
                                if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                    $relsWorksheet = simplexml_load_string(
                                        //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                        $this->securityScan(
                                            $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                        ),
                                        'SimpleXMLElement',
                                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                    );
                                    foreach ($relsWorksheet->Relationship as $ele) {
                                        if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink') {
                                            $hyperlinks[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                    }
                                }

                                // Loop through hyperlinks
                                if ($xmlSheet && $xmlSheet->hyperlinks) {
                                    foreach ($xmlSheet->hyperlinks->hyperlink as $hyperlink) {
                                        // Link url
                                        $linkRel = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');

                                        foreach (\PhpOffice\PhpSpreadsheet\Cell::extractAllCellReferencesInRange($hyperlink['ref']) as $cellReference) {
                                            $cell = $docSheet->getCell($cellReference);
                                            if (isset($linkRel['id'])) {
                                                $hyperlinkUrl = $hyperlinks[(string) $linkRel['id']];
                                                if (isset($hyperlink['location'])) {
                                                    $hyperlinkUrl .= '#' . (string) $hyperlink['location'];
                                                }
                                                $cell->getHyperlink()->setUrl($hyperlinkUrl);
                                            } elseif (isset($hyperlink['location'])) {
                                                $cell->getHyperlink()->setUrl('sheet://' . (string) $hyperlink['location']);
                                            }

                                            // Tooltip
                                            if (isset($hyperlink['tooltip'])) {
                                                $cell->getHyperlink()->setTooltip((string) $hyperlink['tooltip']);
                                            }
                                        }
                                    }
                                }
                            }

                            // Add comments
                            $comments = [];
                            $vmlComments = [];
                            if (!$this->readDataOnly) {
                                // Locate comment relations
                                if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                    $relsWorksheet = simplexml_load_string(
                                        //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                        $this->securityScan(
                                            $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                        ),
                                        'SimpleXMLElement',
                                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                    );
                                    foreach ($relsWorksheet->Relationship as $ele) {
                                        if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments') {
                                            $comments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                        if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing') {
                                            $vmlComments[(string) $ele['Id']] = (string) $ele['Target'];
                                        }
                                    }
                                }

                                // Loop through comments
                                foreach ($comments as $relName => $relPath) {
                                    // Load comments file
                                    $relPath = \PhpOffice\PhpSpreadsheet\Shared\File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);
                                    $commentsFile = simplexml_load_string(
                                        $this->securityScan($this->getFromZipArchive($zip, $relPath)),
                                        'SimpleXMLElement',
                                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                    );

                                    // Utility variables
                                    $authors = [];

                                    // Loop through authors
                                    foreach ($commentsFile->authors->author as $author) {
                                        $authors[] = (string) $author;
                                    }

                                    // Loop through contents
                                    foreach ($commentsFile->commentList->comment as $comment) {
                                        if (!empty($comment['authorId'])) {
                                            $docSheet->getComment((string) $comment['ref'])->setAuthor($authors[(string) $comment['authorId']]);
                                        }
                                        $docSheet->getComment((string) $comment['ref'])->setText($this->parseRichText($comment->text));
                                    }
                                }

                                // Loop through VML comments
                                foreach ($vmlComments as $relName => $relPath) {
                                    // Load VML comments file
                                    $relPath = \PhpOffice\PhpSpreadsheet\Shared\File::realpath(dirname("$dir/$fileWorksheet") . '/' . $relPath);
                                    $vmlCommentsFile = simplexml_load_string(
                                        $this->securityScan($this->getFromZipArchive($zip, $relPath)),
                                        'SimpleXMLElement',
                                        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                    );
                                    $vmlCommentsFile->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

                                    $shapes = $vmlCommentsFile->xpath('//v:shape');
                                    foreach ($shapes as $shape) {
                                        $shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

                                        if (isset($shape['style'])) {
                                            $style = (string) $shape['style'];
                                            $fillColor = strtoupper(substr((string) $shape['fillcolor'], 1));
                                            $column = null;
                                            $row = null;

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

                                            if (($column !== null) && ($row !== null)) {
                                                // Set comment properties
                                                $comment = $docSheet->getCommentByColumnAndRow((string) $column, $row + 1);
                                                $comment->getFillColor()->setRGB($fillColor);

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
                                            }
                                        }
                                    }
                                }

                                // Header/footer images
                                if ($xmlSheet && $xmlSheet->legacyDrawingHF && !$this->readDataOnly) {
                                    if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                        $relsWorksheet = simplexml_load_string(
                                            //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                            $this->securityScan(
                                                $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                            ),
                                            'SimpleXMLElement',
                                            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                        );
                                        $vmlRelationship = '';

                                        foreach ($relsWorksheet->Relationship as $ele) {
                                            if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing') {
                                                $vmlRelationship = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                            }
                                        }

                                        if ($vmlRelationship != '') {
                                            // Fetch linked images
                                            $relsVML = simplexml_load_string(
                                                //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                                $this->securityScan(
                                                    $this->getFromZipArchive($zip, dirname($vmlRelationship) . '/_rels/' . basename($vmlRelationship) . '.rels')
                                                ),
                                                'SimpleXMLElement',
                                                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                            );
                                            $drawings = [];
                                            foreach ($relsVML->Relationship as $ele) {
                                                if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image') {
                                                    $drawings[(string) $ele['Id']] = self::dirAdd($vmlRelationship, $ele['Target']);
                                                }
                                            }

                                            // Fetch VML document
                                            $vmlDrawing = simplexml_load_string(
                                                $this->securityScan($this->getFromZipArchive($zip, $vmlRelationship)),
                                                'SimpleXMLElement',
                                                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                            );
                                            $vmlDrawing->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');

                                            $hfImages = [];

                                            $shapes = $vmlDrawing->xpath('//v:shape');
                                            foreach ($shapes as $idx => $shape) {
                                                $shape->registerXPathNamespace('v', 'urn:schemas-microsoft-com:vml');
                                                $imageData = $shape->xpath('//v:imagedata');
                                                $imageData = $imageData[$idx];

                                                $imageData = $imageData->attributes('urn:schemas-microsoft-com:office:office');
                                                $style = self::toCSSArray((string) $shape['style']);

                                                $hfImages[(string) $shape['id']] = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
                                                if (isset($imageData['title'])) {
                                                    $hfImages[(string) $shape['id']]->setName((string) $imageData['title']);
                                                }

                                                $hfImages[(string) $shape['id']]->setPath('zip://' . \PhpOffice\PhpSpreadsheet\Shared_File::realpath($pFilename) . '#' . $drawings[(string) $imageData['relid']], false);
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
                            if ($zip->locateName(dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')) {
                                $relsWorksheet = simplexml_load_string(
                                    //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                    $this->securityScan(
                                        $this->getFromZipArchive($zip, dirname("$dir/$fileWorksheet") . '/_rels/' . basename($fileWorksheet) . '.rels')
                                    ),
                                    'SimpleXMLElement',
                                    \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                );
                                $drawings = [];
                                foreach ($relsWorksheet->Relationship as $ele) {
                                    if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing') {
                                        $drawings[(string) $ele['Id']] = self::dirAdd("$dir/$fileWorksheet", $ele['Target']);
                                    }
                                }
                                if ($xmlSheet->drawing && !$this->readDataOnly) {
                                    foreach ($xmlSheet->drawing as $drawing) {
                                        $fileDrawing = $drawings[(string) self::getArrayItem($drawing->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'), 'id')];
                                        $relsDrawing = simplexml_load_string(
                                            //~ http://schemas.openxmlformats.org/package/2006/relationships"
                                            $this->securityScan(
                                                $this->getFromZipArchive($zip, dirname($fileDrawing) . '/_rels/' . basename($fileDrawing) . '.rels')
                                            ),
                                            'SimpleXMLElement',
                                            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                        );
                                        $images = [];

                                        if ($relsDrawing && $relsDrawing->Relationship) {
                                            foreach ($relsDrawing->Relationship as $ele) {
                                                if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image') {
                                                    $images[(string) $ele['Id']] = self::dirAdd($fileDrawing, $ele['Target']);
                                                } elseif ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart') {
                                                    if ($this->includeCharts) {
                                                        $charts[self::dirAdd($fileDrawing, $ele['Target'])] = [
                                                            'id' => (string) $ele['Id'],
                                                            'sheet' => $docSheet->getTitle(),
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                        $xmlDrawing = simplexml_load_string(
                                            $this->securityScan($this->getFromZipArchive($zip, $fileDrawing)),
                                            'SimpleXMLElement',
                                            \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                                        )->children('http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing');

                                        if ($xmlDrawing->oneCellAnchor) {
                                            foreach ($xmlDrawing->oneCellAnchor as $oneCellAnchor) {
                                                if ($oneCellAnchor->pic->blipFill) {
                                                    $blip = $oneCellAnchor->pic->blipFill->children('http://schemas.openxmlformats.org/drawingml/2006/main')->blip;
                                                    $xfrm = $oneCellAnchor->pic->spPr->children('http://schemas.openxmlformats.org/drawingml/2006/main')->xfrm;
                                                    $outerShdw = $oneCellAnchor->pic->spPr->children('http://schemas.openxmlformats.org/drawingml/2006/main')->effectLst->outerShdw;
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName((string) self::getArrayItem($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'descr'));
                                                    $objDrawing->setPath(
                                                        'zip://' . \PhpOffice\PhpSpreadsheet\Shared\File::realpath($pFilename) . '#' .
                                                        $images[(string) self::getArrayItem(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'embed'
                                                        )],
                                                        false
                                                    );
                                                    $objDrawing->setCoordinates(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1));
                                                    $objDrawing->setOffsetX(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($oneCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);
                                                    $objDrawing->setWidth(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cx')));
                                                    $objDrawing->setHeight(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cy')));
                                                    if ($xfrm) {
                                                        $objDrawing->setRotation(\PhpOffice\PhpSpreadsheet\Shared\Drawing::angleToDegrees(self::getArrayItem($xfrm->attributes(), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUTopixels(self::getArrayItem($outerShdw->attributes(), 'blurRad')));
                                                        $shadow->setDistance(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUTopixels(self::getArrayItem($outerShdw->attributes(), 'dist')));
                                                        $shadow->setDirection(\PhpOffice\PhpSpreadsheet\Shared\Drawing::angleToDegrees(self::getArrayItem($outerShdw->attributes(), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem($outerShdw->attributes(), 'algn'));
                                                        $shadow->getColor()->setRGB(self::getArrayItem($outerShdw->srgbClr->attributes(), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem($outerShdw->srgbClr->alpha->attributes(), 'val') / 1000);
                                                    }
                                                    $objDrawing->setWorksheet($docSheet);
                                                } else {
                                                    //    ? Can charts be positioned with a oneCellAnchor ?
                                                    $coordinates = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1);
                                                    $offsetX = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($oneCellAnchor->from->colOff);
                                                    $offsetY = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($oneCellAnchor->from->rowOff);
                                                    $width = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cx'));
                                                    $height = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($oneCellAnchor->ext->attributes(), 'cy'));
                                                }
                                            }
                                        }
                                        if ($xmlDrawing->twoCellAnchor) {
                                            foreach ($xmlDrawing->twoCellAnchor as $twoCellAnchor) {
                                                if ($twoCellAnchor->pic->blipFill) {
                                                    $blip = $twoCellAnchor->pic->blipFill->children('http://schemas.openxmlformats.org/drawingml/2006/main')->blip;
                                                    $xfrm = $twoCellAnchor->pic->spPr->children('http://schemas.openxmlformats.org/drawingml/2006/main')->xfrm;
                                                    $outerShdw = $twoCellAnchor->pic->spPr->children('http://schemas.openxmlformats.org/drawingml/2006/main')->effectLst->outerShdw;
                                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                                    $objDrawing->setName((string) self::getArrayItem($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'name'));
                                                    $objDrawing->setDescription((string) self::getArrayItem($twoCellAnchor->pic->nvPicPr->cNvPr->attributes(), 'descr'));
                                                    $objDrawing->setPath(
                                                        'zip://' . \PhpOffice\PhpSpreadsheet\Shared\File::realpath($pFilename) . '#' .
                                                        $images[(string) self::getArrayItem(
                                                            $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships'),
                                                            'embed'
                                                        )],
                                                        false
                                                    );
                                                    $objDrawing->setCoordinates(\PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $twoCellAnchor->from->col) . ($twoCellAnchor->from->row + 1));
                                                    $objDrawing->setOffsetX(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->from->colOff));
                                                    $objDrawing->setOffsetY(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->from->rowOff));
                                                    $objDrawing->setResizeProportional(false);

                                                    if ($xfrm) {
                                                        $objDrawing->setWidth(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($xfrm->ext->attributes(), 'cx')));
                                                        $objDrawing->setHeight(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels(self::getArrayItem($xfrm->ext->attributes(), 'cy')));
                                                        $objDrawing->setRotation(\PhpOffice\PhpSpreadsheet\Shared\Drawing::angleToDegrees(self::getArrayItem($xfrm->attributes(), 'rot')));
                                                    }
                                                    if ($outerShdw) {
                                                        $shadow = $objDrawing->getShadow();
                                                        $shadow->setVisible(true);
                                                        $shadow->setBlurRadius(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUTopixels(self::getArrayItem($outerShdw->attributes(), 'blurRad')));
                                                        $shadow->setDistance(\PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUTopixels(self::getArrayItem($outerShdw->attributes(), 'dist')));
                                                        $shadow->setDirection(\PhpOffice\PhpSpreadsheet\Shared\Drawing::angleToDegrees(self::getArrayItem($outerShdw->attributes(), 'dir')));
                                                        $shadow->setAlignment((string) self::getArrayItem($outerShdw->attributes(), 'algn'));
                                                        $shadow->getColor()->setRGB(self::getArrayItem($outerShdw->srgbClr->attributes(), 'val'));
                                                        $shadow->setAlpha(self::getArrayItem($outerShdw->srgbClr->alpha->attributes(), 'val') / 1000);
                                                    }
                                                    $objDrawing->setWorksheet($docSheet);
                                                } elseif (($this->includeCharts) && ($twoCellAnchor->graphicFrame)) {
                                                    $fromCoordinate = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $twoCellAnchor->from->col) . ($twoCellAnchor->from->row + 1);
                                                    $fromOffsetX = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->from->colOff);
                                                    $fromOffsetY = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->from->rowOff);
                                                    $toCoordinate = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex((string) $twoCellAnchor->to->col) . ($twoCellAnchor->to->row + 1);
                                                    $toOffsetX = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->to->colOff);
                                                    $toOffsetY = \PhpOffice\PhpSpreadsheet\Shared\Drawing::EMUToPixels($twoCellAnchor->to->rowOff);
                                                    $graphic = $twoCellAnchor->graphicFrame->children('http://schemas.openxmlformats.org/drawingml/2006/main')->graphic;
                                                    $chartRef = $graphic->graphicData->children('http://schemas.openxmlformats.org/drawingml/2006/chart')->chart;
                                                    $thisChart = (string) $chartRef->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');

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
                                    }
                                }
                            }

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
                                    if (stripos((string) $definedName, '#REF!') !== false || $extractedRange == '') {
                                        continue;
                                    }

                                    // Some definedNames are only applicable if we are on the same sheet...
                                    if ((string) $definedName['localSheetId'] != '' && (string) $definedName['localSheetId'] == $sheetId) {
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
                                                $rangeSets = preg_split("/'(.*?)'(?:![A-Z0-9]+:[A-Z0-9]+,?)/", $extractedRange, PREG_SPLIT_NO_EMPTY);
                                                $newRangeSets = [];
                                                foreach ($rangeSets as $rangeSet) {
                                                    $range = explode('!', $rangeSet); // FIXME: what if sheetname contains exclamation mark?
                                                    $rangeSet = isset($range[1]) ? $range[1] : $range[0];
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
                                if (($spos = strpos($extractedRange, '!')) !== false) {
                                    $extractedRange = substr($extractedRange, 0, $spos) . str_replace('$', '', substr($extractedRange, $spos));
                                } else {
                                    $extractedRange = str_replace('$', '', $extractedRange);
                                }

                                // Valid range?
                                if (stripos((string) $definedName, '#REF!') !== false || $extractedRange == '') {
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
                                            if ($mapSheetId[(integer) $definedName['localSheetId']] !== null) {
                                                $range = explode('!', (string) $definedName);
                                                if (count($range) == 2) {
                                                    $range[0] = str_replace("''", "'", $range[0]);
                                                    $range[0] = str_replace("'", '', $range[0]);
                                                    if ($worksheet = $docSheet->getParent()->getSheetByName($range[0])) {
                                                        $extractedRange = str_replace('$', '', $range[1]);
                                                        $scope = $docSheet->getParent()->getSheet($mapSheetId[(integer) $definedName['localSheetId']]);
                                                        $excel->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange((string) $definedName['name'], $worksheet, $extractedRange, true, $scope));
                                                    }
                                                }
                                            }
                                            break;
                                    }
                                } elseif (!isset($definedName['localSheetId'])) {
                                    // "Global" definedNames
                                    $locatedSheet = null;
                                    $extractedSheetName = '';
                                    if (strpos((string) $definedName, '!') !== false) {
                                        // Extract sheet name
                                        $extractedSheetName = \PhpOffice\PhpSpreadsheet\Worksheet::extractSheetTitle((string) $definedName, true);
                                        $extractedSheetName = $extractedSheetName[0];

                                        // Locate sheet
                                        $locatedSheet = $excel->getSheetByName($extractedSheetName);

                                        // Modify range
                                        $range = explode('!', $extractedRange);
                                        $extractedRange = isset($range[1]) ? $range[1] : $range[0];
                                    }

                                    if ($locatedSheet !== null) {
                                        $excel->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange((string) $definedName['name'], $locatedSheet, $extractedRange, false));
                                    }
                                }
                            }
                        }
                    }

                    if ((!$this->readDataOnly) || (!empty($this->loadSheetsOnly))) {
                        // active sheet index
                        $activeTab = intval($xmlWorkbook->bookViews->workbookView['activeTab']); // refers to old sheet index

                        // keep active sheet index if sheet is still loaded, else first sheet is set as the active
                        if (isset($mapSheetId[$activeTab]) && $mapSheetId[$activeTab] !== null) {
                            $excel->setActiveSheetIndex($mapSheetId[$activeTab]);
                        } else {
                            if ($excel->getSheetCount() == 0) {
                                $excel->createSheet();
                            }
                            $excel->setActiveSheetIndex(0);
                        }
                    }
                    break;
            }
        }

        if (!$this->readDataOnly) {
            $contentTypes = simplexml_load_string(
                $this->securityScan(
                    $this->getFromZipArchive($zip, '[Content_Types].xml')
                ),
                'SimpleXMLElement',
                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
            );
            foreach ($contentTypes->Override as $contentType) {
                switch ($contentType['ContentType']) {
                    case 'application/vnd.openxmlformats-officedocument.drawingml.chart+xml':
                        if ($this->includeCharts) {
                            $chartEntryRef = ltrim($contentType['PartName'], '/');
                            $chartElements = simplexml_load_string(
                                $this->securityScan(
                                    $this->getFromZipArchive($zip, $chartEntryRef)
                                ),
                                'SimpleXMLElement',
                                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
                            );
                            $objChart = \PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart::readChart($chartElements, basename($chartEntryRef, '.xml'));

                            if (isset($charts[$chartEntryRef])) {
                                $chartPositionRef = $charts[$chartEntryRef]['sheet'] . '!' . $charts[$chartEntryRef]['id'];
                                if (isset($chartDetails[$chartPositionRef])) {
                                    $excel->getSheetByName($charts[$chartEntryRef]['sheet'])->addChart($objChart);
                                    $objChart->setWorksheet($excel->getSheetByName($charts[$chartEntryRef]['sheet']));
                                    $objChart->setTopLeftPosition($chartDetails[$chartPositionRef]['fromCoordinate'], $chartDetails[$chartPositionRef]['fromOffsetX'], $chartDetails[$chartPositionRef]['fromOffsetY']);
                                    $objChart->setBottomRightPosition($chartDetails[$chartPositionRef]['toCoordinate'], $chartDetails[$chartPositionRef]['toOffsetX'], $chartDetails[$chartPositionRef]['toOffsetY']);
                                }
                            }
                        }
                }
            }
        }

        $zip->close();

        return $excel;
    }

    private static function readColor($color, $background = false)
    {
        if (isset($color['rgb'])) {
            return (string) $color['rgb'];
        } elseif (isset($color['indexed'])) {
            return \PhpOffice\PhpSpreadsheet\Style\Color::indexedColor($color['indexed'] - 7, $background)->getARGB();
        } elseif (isset($color['theme'])) {
            if (self::$theme !== null) {
                $returnColour = self::$theme->getColourByIndex((int) $color['theme']);
                if (isset($color['tint'])) {
                    $tintAdjust = (float) $color['tint'];
                    $returnColour = \PhpOffice\PhpSpreadsheet\Style\Color::changeBrightness($returnColour, $tintAdjust);
                }

                return 'FF' . $returnColour;
            }
        }

        if ($background) {
            return 'FFFFFFFF';
        }

        return 'FF000000';
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Style $docStyle
     * @param \stdClass|\SimpleXMLElement $style
     */
    private static function readStyle(\PhpOffice\PhpSpreadsheet\Style $docStyle, $style)
    {
        $docStyle->getNumberFormat()->setFormatCode($style->numFmt);

        // font
        if (isset($style->font)) {
            $docStyle->getFont()->setName((string) $style->font->name['val']);
            $docStyle->getFont()->setSize((string) $style->font->sz['val']);
            if (isset($style->font->b)) {
                $docStyle->getFont()->setBold(!isset($style->font->b['val']) || self::boolean((string) $style->font->b['val']));
            }
            if (isset($style->font->i)) {
                $docStyle->getFont()->setItalic(!isset($style->font->i['val']) || self::boolean((string) $style->font->i['val']));
            }
            if (isset($style->font->strike)) {
                $docStyle->getFont()->setStrikethrough(!isset($style->font->strike['val']) || self::boolean((string) $style->font->strike['val']));
            }
            $docStyle->getFont()->getColor()->setARGB(self::readColor($style->font->color));

            if (isset($style->font->u) && !isset($style->font->u['val'])) {
                $docStyle->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
            } elseif (isset($style->font->u) && isset($style->font->u['val'])) {
                $docStyle->getFont()->setUnderline((string) $style->font->u['val']);
            }

            if (isset($style->font->vertAlign) && isset($style->font->vertAlign['val'])) {
                $vertAlign = strtolower((string) $style->font->vertAlign['val']);
                if ($vertAlign == 'superscript') {
                    $docStyle->getFont()->setSuperScript(true);
                }
                if ($vertAlign == 'subscript') {
                    $docStyle->getFont()->setSubScript(true);
                }
            }
        }

        // fill
        if (isset($style->fill)) {
            if ($style->fill->gradientFill) {
                $gradientFill = $style->fill->gradientFill[0];
                if (!empty($gradientFill['type'])) {
                    $docStyle->getFill()->setFillType((string) $gradientFill['type']);
                }
                $docStyle->getFill()->setRotation(floatval($gradientFill['degree']));
                $gradientFill->registerXPathNamespace('sml', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $docStyle->getFill()->getStartColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=0]'))->color));
                $docStyle->getFill()->getEndColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=1]'))->color));
            } elseif ($style->fill->patternFill) {
                $patternType = (string) $style->fill->patternFill['patternType'] != '' ? (string) $style->fill->patternFill['patternType'] : 'solid';
                $docStyle->getFill()->setFillType($patternType);
                if ($style->fill->patternFill->fgColor) {
                    $docStyle->getFill()->getStartColor()->setARGB(self::readColor($style->fill->patternFill->fgColor, true));
                } else {
                    $docStyle->getFill()->getStartColor()->setARGB('FF000000');
                }
                if ($style->fill->patternFill->bgColor) {
                    $docStyle->getFill()->getEndColor()->setARGB(self::readColor($style->fill->patternFill->bgColor, true));
                }
            }
        }

        // border
        if (isset($style->border)) {
            $diagonalUp = self::boolean((string) $style->border['diagonalUp']);
            $diagonalDown = self::boolean((string) $style->border['diagonalDown']);
            if (!$diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(\PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_NONE);
            } elseif ($diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(\PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_UP);
            } elseif (!$diagonalUp && $diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(\PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_DOWN);
            } else {
                $docStyle->getBorders()->setDiagonalDirection(\PhpOffice\PhpSpreadsheet\Style\Borders::DIAGONAL_BOTH);
            }
            self::readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
            self::readBorder($docStyle->getBorders()->getRight(), $style->border->right);
            self::readBorder($docStyle->getBorders()->getTop(), $style->border->top);
            self::readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
            self::readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
        }

        // alignment
        if (isset($style->alignment)) {
            $docStyle->getAlignment()->setHorizontal((string) $style->alignment['horizontal']);
            $docStyle->getAlignment()->setVertical((string) $style->alignment['vertical']);

            $textRotation = 0;
            if ((int) $style->alignment['textRotation'] <= 90) {
                $textRotation = (int) $style->alignment['textRotation'];
            } elseif ((int) $style->alignment['textRotation'] > 90) {
                $textRotation = 90 - (int) $style->alignment['textRotation'];
            }

            $docStyle->getAlignment()->setTextRotation(intval($textRotation));
            $docStyle->getAlignment()->setWrapText(self::boolean((string) $style->alignment['wrapText']));
            $docStyle->getAlignment()->setShrinkToFit(self::boolean((string) $style->alignment['shrinkToFit']));
            $docStyle->getAlignment()->setIndent(intval((string) $style->alignment['indent']) > 0 ? intval((string) $style->alignment['indent']) : 0);
            $docStyle->getAlignment()->setReadorder(intval((string) $style->alignment['readingOrder']) > 0 ? intval((string) $style->alignment['readingOrder']) : 0);
        }

        // protection
        if (isset($style->protection)) {
            if (isset($style->protection['locked'])) {
                if (self::boolean((string) $style->protection['locked'])) {
                    $docStyle->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                }
            }

            if (isset($style->protection['hidden'])) {
                if (self::boolean((string) $style->protection['hidden'])) {
                    $docStyle->getProtection()->setHidden(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setHidden(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                }
            }
        }

        // top-level style settings
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix($style->quotePrefix);
        }
    }

    private static function readBorder($docBorder, $eleBorder)
    {
        if (isset($eleBorder['style'])) {
            $docBorder->setBorderStyle((string) $eleBorder['style']);
        }
        if (isset($eleBorder->color)) {
            $docBorder->getColor()->setARGB(self::readColor($eleBorder->color));
        }
    }

    private function parseRichText($is = null)
    {
        $value = new \PhpOffice\PhpSpreadsheet\RichText();

        if (isset($is->t)) {
            $value->createText(\PhpOffice\PhpSpreadsheet\Shared\StringHelper::controlCharacterOOXML2PHP((string) $is->t));
        } else {
            if (is_object($is->r)) {
                foreach ($is->r as $run) {
                    if (!isset($run->rPr)) {
                        $objText = $value->createText(\PhpOffice\PhpSpreadsheet\Shared\StringHelper::controlCharacterOOXML2PHP((string) $run->t));
                    } else {
                        $objText = $value->createTextRun(\PhpOffice\PhpSpreadsheet\Shared\StringHelper::controlCharacterOOXML2PHP((string) $run->t));

                        if (isset($run->rPr->rFont['val'])) {
                            $objText->getFont()->setName((string) $run->rPr->rFont['val']);
                        }
                        if (isset($run->rPr->sz['val'])) {
                            $objText->getFont()->setSize((string) $run->rPr->sz['val']);
                        }
                        if (isset($run->rPr->color)) {
                            $objText->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(self::readColor($run->rPr->color)));
                        }
                        if ((isset($run->rPr->b['val']) && self::boolean((string) $run->rPr->b['val'])) ||
                            (isset($run->rPr->b) && !isset($run->rPr->b['val']))) {
                            $objText->getFont()->setBold(true);
                        }
                        if ((isset($run->rPr->i['val']) && self::boolean((string) $run->rPr->i['val'])) ||
                            (isset($run->rPr->i) && !isset($run->rPr->i['val']))) {
                            $objText->getFont()->setItalic(true);
                        }
                        if (isset($run->rPr->vertAlign) && isset($run->rPr->vertAlign['val'])) {
                            $vertAlign = strtolower((string) $run->rPr->vertAlign['val']);
                            if ($vertAlign == 'superscript') {
                                $objText->getFont()->setSuperScript(true);
                            }
                            if ($vertAlign == 'subscript') {
                                $objText->getFont()->setSubScript(true);
                            }
                        }
                        if (isset($run->rPr->u) && !isset($run->rPr->u['val'])) {
                            $objText->getFont()->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
                        } elseif (isset($run->rPr->u) && isset($run->rPr->u['val'])) {
                            $objText->getFont()->setUnderline((string) $run->rPr->u['val']);
                        }
                        if ((isset($run->rPr->strike['val']) && self::boolean((string) $run->rPr->strike['val'])) ||
                            (isset($run->rPr->strike) && !isset($run->rPr->strike['val']))) {
                            $objText->getFont()->setStrikethrough(true);
                        }
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $excel
     */
    private function readRibbon(\PhpOffice\PhpSpreadsheet\Spreadsheet $excel, $customUITarget, $zip)
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
                $this->securityScan($dataRels),
                'SimpleXMLElement',
                \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
            );
            if ($UIRels) {
                // we need to save id and target to avoid parsing customUI.xml and "guess" if it's a pseudo callback who load the image
                foreach ($UIRels->Relationship as $ele) {
                    if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image') {
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
                $excel->setRibbonBinObjects(null);
            }
        } else {
            $excel->setRibbonXMLData(null);
            $excel->setRibbonBinObjects(null);
        }
    }

    private static function getArrayItem($array, $key = 0)
    {
        return isset($array[$key]) ? $array[$key] : null;
    }

    private static function dirAdd($base, $add)
    {
        return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
    }

    private static function toCSSArray($style)
    {
        $style = str_replace(["\r", "\n"], '', $style);

        $temp = explode(';', $style);
        $style = [];
        foreach ($temp as $item) {
            $item = explode(':', $item);

            if (strpos($item[1], 'px') !== false) {
                $item[1] = str_replace('px', '', $item[1]);
            }
            if (strpos($item[1], 'pt') !== false) {
                $item[1] = str_replace('pt', '', $item[1]);
                $item[1] = \PhpOffice\PhpSpreadsheet\Shared\Font::fontSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'in') !== false) {
                $item[1] = str_replace('in', '', $item[1]);
                $item[1] = \PhpOffice\PhpSpreadsheet\Shared\Font::inchSizeToPixels($item[1]);
            }
            if (strpos($item[1], 'cm') !== false) {
                $item[1] = str_replace('cm', '', $item[1]);
                $item[1] = \PhpOffice\PhpSpreadsheet\Shared\Font::centimeterSizeToPixels($item[1]);
            }

            $style[$item[0]] = $item[1];
        }

        return $style;
    }

    private static function boolean($value = null)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === 'true' || $value === 'TRUE';
    }
}
