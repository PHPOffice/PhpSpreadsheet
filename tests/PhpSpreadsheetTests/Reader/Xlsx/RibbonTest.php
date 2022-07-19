<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RibbonTest extends AbstractFunctional
{
    /**
     * Test read/rewrite spreadsheet with ribbon data.
     */
    public function testRibbon(): void
    {
        // The following file is downloaded, with the author's
        // permission, from:
        // https://www.rondebruin.nl/win/s2/win003.htm
        // It is renamed, including changing its extension to zip.
        $filename = 'tests/data/Reader/XLSX/ribbon.donotopen.zip';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        self::assertTrue($spreadsheet->hasRibbon());
        $target = $spreadsheet->getRibbonXMLData('target');
        self::assertSame('customUI/customUI.xml', $target);
        $data = $spreadsheet->getRibbonXMLData('data');
        self::assertIsString($data);
        self::assertSame(1522, strlen($data));
        $vbaCode = (string) $spreadsheet->getMacrosCode();
        self::assertSame(13312, strlen($vbaCode));
        self::assertNull($spreadsheet->getRibbonBinObjects());
        self::assertNull($spreadsheet->getRibbonBinObjects('names'));
        self::assertNull($spreadsheet->getRibbonBinObjects('data'));
        self::assertEmpty($spreadsheet->getRibbonBinObjects('types'));
        self::assertNull($spreadsheet->getRibbonBinObjects('xxxxx'));

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertTrue($reloadedSpreadsheet->hasRibbon());
        $ribbonData = $reloadedSpreadsheet->getRibbonXmlData();
        self::assertIsArray($ribbonData);
        self::assertSame($target, $ribbonData['target'] ?? '');
        self::assertSame($data, $ribbonData['data'] ?? '');
        self::assertSame($vbaCode, $reloadedSpreadsheet->getMacrosCode());
        self::assertNull($reloadedSpreadsheet->getRibbonBinObjects());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Same as above but discard macros.
     */
    public function testDiscardMacros(): void
    {
        $filename = 'tests/data/Reader/XLSX/ribbon.donotopen.zip';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        self::assertTrue($spreadsheet->hasRibbon());
        $target = $spreadsheet->getRibbonXMLData('target');
        self::assertSame('customUI/customUI.xml', $target);
        $data = $spreadsheet->getRibbonXMLData('data');
        self::assertIsString($data);
        self::assertSame(1522, strlen($data));
        $vbaCode = (string) $spreadsheet->getMacrosCode();
        self::assertSame(13312, strlen($vbaCode));
        $spreadsheet->discardMacros();

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertTrue($reloadedSpreadsheet->hasRibbon());
        $ribbonData = $reloadedSpreadsheet->getRibbonXmlData();
        self::assertIsArray($ribbonData);
        self::assertSame($target, $ribbonData['target'] ?? '');
        self::assertSame($data, $ribbonData['data'] ?? '');
        self::assertNull($reloadedSpreadsheet->getMacrosCode());
        self::assertNull($reloadedSpreadsheet->getRibbonBinObjects());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
