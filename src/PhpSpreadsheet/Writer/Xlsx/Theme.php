<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Theme as SpreadsheetTheme;

class Theme extends WriterPart
{
    /**
     * Write theme to XML format.
     *
     * @return string XML Output
     */
    public function writeTheme(Spreadsheet $spreadsheet)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }
        $theme = $spreadsheet->getTheme();

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // a:theme
        $objWriter->startElement('a:theme');
        $objWriter->writeAttribute('xmlns:a', Namespaces::DRAWINGML);
        $objWriter->writeAttribute('name', 'Office Theme');

        // a:themeElements
        $objWriter->startElement('a:themeElements');

        // a:clrScheme
        $objWriter->startElement('a:clrScheme');
        $objWriter->writeAttribute('name', $theme->getThemeColorName());

        $this->writeColourScheme($objWriter, $theme);

        $objWriter->endElement();

        // a:fontScheme
        $objWriter->startElement('a:fontScheme');
        $objWriter->writeAttribute('name', $theme->getThemeFontName());

        // a:majorFont
        $objWriter->startElement('a:majorFont');
        $this->writeFonts(
            $objWriter,
            $theme->getMajorFontLatin(),
            $theme->getMajorFontEastAsian(),
            $theme->getMajorFontComplexScript(),
            $theme->getMajorFontSubstitutions()
        );
        $objWriter->endElement(); // a:majorFont

        // a:minorFont
        $objWriter->startElement('a:minorFont');
        $this->writeFonts(
            $objWriter,
            $theme->getMinorFontLatin(),
            $theme->getMinorFontEastAsian(),
            $theme->getMinorFontComplexScript(),
            $theme->getMinorFontSubstitutions()
        );
        $objWriter->endElement(); // a:minorFont

        $objWriter->endElement(); // a:fontScheme

        // a:fmtScheme
        $objWriter->startElement('a:fmtScheme');
        $objWriter->writeAttribute('name', 'Office');

        // a:fillStyleLst
        $objWriter->startElement('a:fillStyleLst');

        // a:solidFill
        $objWriter->startElement('a:solidFill');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:gradFill
        $objWriter->startElement('a:gradFill');
        $objWriter->writeAttribute('rotWithShape', '1');

        // a:gsLst
        $objWriter->startElement('a:gsLst');

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '0');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '50000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '300000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '35000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '37000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '300000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '100000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '15000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '350000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:lin
        $objWriter->startElement('a:lin');
        $objWriter->writeAttribute('ang', '16200000');
        $objWriter->writeAttribute('scaled', '1');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:gradFill
        $objWriter->startElement('a:gradFill');
        $objWriter->writeAttribute('rotWithShape', '1');

        // a:gsLst
        $objWriter->startElement('a:gsLst');

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '0');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '51000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '130000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '80000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '93000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '130000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '100000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '94000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '135000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:lin
        $objWriter->startElement('a:lin');
        $objWriter->writeAttribute('ang', '16200000');
        $objWriter->writeAttribute('scaled', '0');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:lnStyleLst
        $objWriter->startElement('a:lnStyleLst');

        // a:ln
        $objWriter->startElement('a:ln');
        $objWriter->writeAttribute('w', '9525');
        $objWriter->writeAttribute('cap', 'flat');
        $objWriter->writeAttribute('cmpd', 'sng');
        $objWriter->writeAttribute('algn', 'ctr');

        // a:solidFill
        $objWriter->startElement('a:solidFill');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '95000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '105000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:prstDash
        $objWriter->startElement('a:prstDash');
        $objWriter->writeAttribute('val', 'solid');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:ln
        $objWriter->startElement('a:ln');
        $objWriter->writeAttribute('w', '25400');
        $objWriter->writeAttribute('cap', 'flat');
        $objWriter->writeAttribute('cmpd', 'sng');
        $objWriter->writeAttribute('algn', 'ctr');

        // a:solidFill
        $objWriter->startElement('a:solidFill');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:prstDash
        $objWriter->startElement('a:prstDash');
        $objWriter->writeAttribute('val', 'solid');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:ln
        $objWriter->startElement('a:ln');
        $objWriter->writeAttribute('w', '38100');
        $objWriter->writeAttribute('cap', 'flat');
        $objWriter->writeAttribute('cmpd', 'sng');
        $objWriter->writeAttribute('algn', 'ctr');

        // a:solidFill
        $objWriter->startElement('a:solidFill');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:prstDash
        $objWriter->startElement('a:prstDash');
        $objWriter->writeAttribute('val', 'solid');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:effectStyleLst
        $objWriter->startElement('a:effectStyleLst');

        // a:effectStyle
        $objWriter->startElement('a:effectStyle');

        // a:effectLst
        $objWriter->startElement('a:effectLst');

        // a:outerShdw
        $objWriter->startElement('a:outerShdw');
        $objWriter->writeAttribute('blurRad', '40000');
        $objWriter->writeAttribute('dist', '20000');
        $objWriter->writeAttribute('dir', '5400000');
        $objWriter->writeAttribute('rotWithShape', '0');

        // a:srgbClr
        $objWriter->startElement('a:srgbClr');
        $objWriter->writeAttribute('val', '000000');

        // a:alpha
        $objWriter->startElement('a:alpha');
        $objWriter->writeAttribute('val', '38000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:effectStyle
        $objWriter->startElement('a:effectStyle');

        // a:effectLst
        $objWriter->startElement('a:effectLst');

        // a:outerShdw
        $objWriter->startElement('a:outerShdw');
        $objWriter->writeAttribute('blurRad', '40000');
        $objWriter->writeAttribute('dist', '23000');
        $objWriter->writeAttribute('dir', '5400000');
        $objWriter->writeAttribute('rotWithShape', '0');

        // a:srgbClr
        $objWriter->startElement('a:srgbClr');
        $objWriter->writeAttribute('val', '000000');

        // a:alpha
        $objWriter->startElement('a:alpha');
        $objWriter->writeAttribute('val', '35000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:effectStyle
        $objWriter->startElement('a:effectStyle');

        // a:effectLst
        $objWriter->startElement('a:effectLst');

        // a:outerShdw
        $objWriter->startElement('a:outerShdw');
        $objWriter->writeAttribute('blurRad', '40000');
        $objWriter->writeAttribute('dist', '23000');
        $objWriter->writeAttribute('dir', '5400000');
        $objWriter->writeAttribute('rotWithShape', '0');

        // a:srgbClr
        $objWriter->startElement('a:srgbClr');
        $objWriter->writeAttribute('val', '000000');

        // a:alpha
        $objWriter->startElement('a:alpha');
        $objWriter->writeAttribute('val', '35000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:scene3d
        $objWriter->startElement('a:scene3d');

        // a:camera
        $objWriter->startElement('a:camera');
        $objWriter->writeAttribute('prst', 'orthographicFront');

        // a:rot
        $objWriter->startElement('a:rot');
        $objWriter->writeAttribute('lat', '0');
        $objWriter->writeAttribute('lon', '0');
        $objWriter->writeAttribute('rev', '0');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:lightRig
        $objWriter->startElement('a:lightRig');
        $objWriter->writeAttribute('rig', 'threePt');
        $objWriter->writeAttribute('dir', 't');

        // a:rot
        $objWriter->startElement('a:rot');
        $objWriter->writeAttribute('lat', '0');
        $objWriter->writeAttribute('lon', '0');
        $objWriter->writeAttribute('rev', '1200000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:sp3d
        $objWriter->startElement('a:sp3d');

        // a:bevelT
        $objWriter->startElement('a:bevelT');
        $objWriter->writeAttribute('w', '63500');
        $objWriter->writeAttribute('h', '25400');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:bgFillStyleLst
        $objWriter->startElement('a:bgFillStyleLst');

        // a:solidFill
        $objWriter->startElement('a:solidFill');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');
        $objWriter->endElement();

        $objWriter->endElement();

        // a:gradFill
        $objWriter->startElement('a:gradFill');
        $objWriter->writeAttribute('rotWithShape', '1');

        // a:gsLst
        $objWriter->startElement('a:gsLst');

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '0');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '40000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '350000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '40000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '45000');
        $objWriter->endElement();

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '99000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '350000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '100000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '20000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '255000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:path
        $objWriter->startElement('a:path');
        $objWriter->writeAttribute('path', 'circle');

        // a:fillToRect
        $objWriter->startElement('a:fillToRect');
        $objWriter->writeAttribute('l', '50000');
        $objWriter->writeAttribute('t', '-80000');
        $objWriter->writeAttribute('r', '50000');
        $objWriter->writeAttribute('b', '180000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gradFill
        $objWriter->startElement('a:gradFill');
        $objWriter->writeAttribute('rotWithShape', '1');

        // a:gsLst
        $objWriter->startElement('a:gsLst');

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '0');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:tint
        $objWriter->startElement('a:tint');
        $objWriter->writeAttribute('val', '80000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '300000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:gs
        $objWriter->startElement('a:gs');
        $objWriter->writeAttribute('pos', '100000');

        // a:schemeClr
        $objWriter->startElement('a:schemeClr');
        $objWriter->writeAttribute('val', 'phClr');

        // a:shade
        $objWriter->startElement('a:shade');
        $objWriter->writeAttribute('val', '30000');
        $objWriter->endElement();

        // a:satMod
        $objWriter->startElement('a:satMod');
        $objWriter->writeAttribute('val', '200000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:path
        $objWriter->startElement('a:path');
        $objWriter->writeAttribute('path', 'circle');

        // a:fillToRect
        $objWriter->startElement('a:fillToRect');
        $objWriter->writeAttribute('l', '50000');
        $objWriter->writeAttribute('t', '50000');
        $objWriter->writeAttribute('r', '50000');
        $objWriter->writeAttribute('b', '50000');
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // a:objectDefaults
        $objWriter->writeElement('a:objectDefaults', null);

        // a:extraClrSchemeLst
        $objWriter->writeElement('a:extraClrSchemeLst', null);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write fonts to XML format.
     *
     * @param string[] $fontSet
     */
    private function writeFonts(XMLWriter $objWriter, string $latinFont, string $eastAsianFont, string $complexScriptFont, array $fontSet): void
    {
        // a:latin
        $objWriter->startElement('a:latin');
        $objWriter->writeAttribute('typeface', $latinFont);
        $objWriter->endElement();

        // a:ea
        $objWriter->startElement('a:ea');
        $objWriter->writeAttribute('typeface', $eastAsianFont);
        $objWriter->endElement();

        // a:cs
        $objWriter->startElement('a:cs');
        $objWriter->writeAttribute('typeface', $complexScriptFont);
        $objWriter->endElement();

        foreach ($fontSet as $fontScript => $typeface) {
            $objWriter->startElement('a:font');
            $objWriter->writeAttribute('script', $fontScript);
            $objWriter->writeAttribute('typeface', $typeface);
            $objWriter->endElement();
        }
    }

    /**
     * Write colour scheme to XML format.
     */
    private function writeColourScheme(XMLWriter $objWriter, SpreadsheetTheme $theme): void
    {
        $themeArray = $theme->getThemeColors();
        // a:dk1
        $objWriter->startElement('a:dk1');
        $objWriter->startElement('a:sysClr');
        $objWriter->writeAttribute('val', 'windowText');
        $objWriter->writeAttribute('lastClr', $themeArray['dk1'] ?? '000000');
        $objWriter->endElement(); // a:sysClr
        $objWriter->endElement(); // a:dk1

        // a:lt1
        $objWriter->startElement('a:lt1');
        $objWriter->startElement('a:sysClr');
        $objWriter->writeAttribute('val', 'window');
        $objWriter->writeAttribute('lastClr', $themeArray['lt1'] ?? 'FFFFFF');
        $objWriter->endElement(); // a:sysClr
        $objWriter->endElement(); // a:lt1

        foreach ($themeArray as $colourName => $colourValue) {
            if ($colourName !== 'dk1' && $colourName !== 'lt1') {
                $objWriter->startElement('a:' . $colourName);
                $objWriter->startElement('a:srgbClr');
                $objWriter->writeAttribute('val', $colourValue);
                $objWriter->endElement(); // a:srgbClr
                $objWriter->endElement(); // a:$colourName
            }
        }
    }
}
