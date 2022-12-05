<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvLoadFromStringTest extends TestCase
{
    public function testLoadFromString(): void
    {
        $data = <<<EOF
            1,2,3
            4,2+3,6
            "7 , 8", 9, 10
            11,"12
            13",14
            EOF;
        $reader = new Csv();
        $spreadsheet = $reader->loadSpreadsheetFromString($data);
        $sheet = $spreadsheet->getActiveSheet();
        self::AssertSame('2+3', $sheet->getCell('B2')->getValue());
        self::AssertSame('7 , 8', $sheet->getCell('A3')->getValue());
        self::AssertSame("12\n13", $sheet->getCell('B4')->getValue());
    }
}
