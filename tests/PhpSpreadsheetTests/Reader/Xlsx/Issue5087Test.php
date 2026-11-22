<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue5087Test extends TestCase
{
    private static string $filename = 'tests/data/Reader/XLSX/issue.5087.xlsx';

    protected static bool $alwaysTrue = true;

    public function testReadBoolDefinedNamesCorrectly(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::$filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(2, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('hello', $sheet->getCell('A2')->getCalculatedValue());
        self::assertSame(-2.5, $sheet->getCell('A3')->getCalculatedValue());
        self::assertSame(self::$alwaysTrue, $sheet->getCell('A4')->getCalculatedValue());
        self::assertSame(42, $sheet->getCell('A5')->getCalculatedValue(), 'reference to weird sheet name');
        self::assertSame(42, $sheet->getCell('A6')->getCalculatedValue(), 'reference to defined name on weird sheet name');

        $spreadsheet->disconnectWorksheets();
    }
}
