<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Biff8CoverTest extends TestCase
{
    protected function tearDown(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    public function testBiff8Coverage(): void
    {
        $filename = 'tests/data/Reader/XLS/biff8cover.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=SUM({1;2;3;4;5})', $sheet->getCell('A1')->getValue());
        self::assertSame(15, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame(
            '=VLOOKUP("hello",'
            . '{"what",1;"why",TRUE;"hello","there";"when",FALSE}'
            . ',2,FALSE)',
            $sheet->getCell('C1')->getValue()
        );
        self::assertSame('there', $sheet->getCell('C1')->getCalculatedValue());
        self::assertSame(2, $sheet->getCell('A3')->getValue());
        self::assertTrue(
            $sheet->getStyle('A3')->getFont()->getSuperscript()
        );
        self::assertSame('n', $sheet->getCell('B3')->getValue());
        self::assertTrue(
            $sheet->getStyle('B3')->getFont()->getSubscript()
        );

        $spreadsheet->disconnectWorksheets();
    }
}
