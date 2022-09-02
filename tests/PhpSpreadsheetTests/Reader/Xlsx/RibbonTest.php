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
        self::assertSame(1522, strlen(/** @scrutinizer ignore-type */ $data));
        $vbaCode = (string) $spreadsheet->getMacrosCode();
        self::assertSame(13312, strlen($vbaCode));
        self::assertNull($spreadsheet->getRibbonBinObjects());
        foreach (['names', 'data', 'xxxxx'] as $type) {
            self::assertNull($spreadsheet->getRibbonBinObjects($type), "Expecting null when type is $type");
        }
        self::assertEmpty($spreadsheet->getRibbonBinObjects('types'));

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
        self::assertSame(1522, strlen(/** @scrutinizer ignore-type */ $data));
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
