<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class NamedRangeTest extends TestCase
{
    public static function testBug1686b(): void
    {
        $xlsxFile = 'tests/data/Reader/XLSX/bug1686b.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($xlsxFile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals(2.1, $sheet->getCell('A1')->getCalculatedValue());
        self::assertEquals('#REF!', $sheet->getCell('A2')->getCalculatedValue());
        self::assertEquals('#REF!', $sheet->getCell('A3')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A4')->getCalculatedValue());
        self::assertEquals('#REF!', $sheet->getCell('A5')->getCalculatedValue());
    }
}
