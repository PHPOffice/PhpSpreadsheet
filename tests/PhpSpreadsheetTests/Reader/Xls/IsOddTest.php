<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class IsOddTest extends TestCase
{
    public function testIsOdd(): void
    {
        // Excel Xls treats ISODD and ISEVEN as 'Add-in functions'
        // PhpSpreadsheet does not currently handle writing them.
        // It should, however, read them correctly.
        $filename = 'tests/data/Reader/XLS/isodd.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=ISODD(5)', $sheet->getCell('A1')->getValue());
        self::assertTrue($sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('=ISEVEN(5)', $sheet->getCell('B1')->getValue());
        self::assertFalse($sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
