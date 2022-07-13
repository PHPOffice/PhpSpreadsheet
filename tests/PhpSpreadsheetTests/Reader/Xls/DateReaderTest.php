<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateReaderTest extends TestCase
{
    protected function tearDown(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    public function testReadExcel1900Spreadsheet(): void
    {
        $filename = 'tests/data/Reader/XLS/1900_Calendar.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);

        self::assertSame(Date::CALENDAR_WINDOWS_1900, $spreadsheet->getExcelCalendar());

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame(44562, $worksheet->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet->getCell('A1')->getFormattedValue());
        self::assertSame(44926, $worksheet->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet->getCell('A2')->getFormattedValue());
    }

    public function testReadExcel1904Spreadsheet(): void
    {
        $filename = 'tests/data/Reader/XLS/1904_Calendar.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);

        self::assertSame(Date::CALENDAR_MAC_1904, $spreadsheet->getExcelCalendar());

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame(43100, $worksheet->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet->getCell('A1')->getFormattedValue());
        self::assertSame(43464, $worksheet->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet->getCell('A2')->getFormattedValue());
    }
}
