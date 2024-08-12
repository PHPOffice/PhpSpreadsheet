<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3730Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3730.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl\\_rels\\workbook.xml.rels'; // no idea why backslash
        $data = file_get_contents($file);
        // confirm that file contains expected absolute reference
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('Target="/xl/sharedStrings.xml"', $data);
            self::assertStringContainsString('Target="/xl/styles.xml"', $data);
            self::assertStringContainsString('Target="/xl/worksheets/sheet0.xml"', $data);
        }
    }

    public function testInfo(): void
    {
        $reader = new Xlsx();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info1 = $workSheetInfo[0];
        self::assertEquals('promedia postlogs wk 2-12', $info1['worksheetName']);
        self::assertEquals('O', $info1['lastColumnLetter']);
        self::assertEquals(14, $info1['lastColumnIndex']);
        self::assertEquals(99, $info1['totalRows']);
        self::assertEquals(15, $info1['totalColumns']);
    }

    public function testSheetNames(): void
    {
        $reader = new Xlsx();
        $worksheetNames = $reader->listWorksheetNames(self::$testbook);
        $expected = [
            'promedia postlogs wk 2-12',
        ];
        self::assertEquals($expected, $worksheetNames);
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheets = $spreadsheet->getAllSheets();
        self::assertCount(1, $sheets);
        $sheet = $spreadsheet->getSheetByNameOrThrow('promedia postlogs wk 2-12');
        self::assertSame('ProMedia Group - DR', $sheet->getCell('G7')->getValue());
        self::assertSame('Arial', $sheet->getStyle('G7')->getFont()->getName());
        self::assertEquals(8, $sheet->getStyle('G7')->getFont()->getSize());
        $spreadsheet->disconnectWorksheets();
    }
}
