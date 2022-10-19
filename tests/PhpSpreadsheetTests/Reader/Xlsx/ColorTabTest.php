<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class ColorTabTest extends TestCase
{
    public function testIssue2316(): void
    {
        $filename = 'tests/data/Reader/XLSX/colortabs.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $unparsed = $spreadsheet->getUnparsedLoadedData();
        self::assertArrayHasKey('sheets', $unparsed);
        self::assertArrayHasKey('Worksheet', $unparsed['sheets']);
        self::assertArrayNotHasKey('vmlDrawings', $unparsed['sheets']['Worksheet']);

        // theme color
        self::assertSame('FF548135', $spreadsheet->getSheet(0)->getTabColor()->getArgb());
        // rgb color
        self::assertSame('FFFFC000', $spreadsheet->getSheet(1)->getTabColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }

    public function testIssue3125(): void
    {
        // Very artificial - no real sample file to go with issue.
        $filename = 'tests/data/Reader/XLSX/colortabs.badbr.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $unparsed = $spreadsheet->getUnparsedLoadedData();
        self::assertArrayHasKey('sheets', $unparsed);
        self::assertArrayHasKey('Worksheet', $unparsed['sheets']);
        // Before fix, vml in sample file was unparseable as xml (unclosed br tag),
        // so condition below would fail.
        self::assertArrayNotHasKey('vmlDrawings', $unparsed['sheets']['Worksheet']);

        // theme color
        self::assertSame('FF548135', $spreadsheet->getSheet(0)->getTabColor()->getArgb());
        // rgb color
        self::assertSame('FFFFC000', $spreadsheet->getSheet(1)->getTabColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }
}
