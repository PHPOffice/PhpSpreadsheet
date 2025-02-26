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
        $spreadsheet->disconnectWorksheets();
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
        $spreadsheet->disconnectWorksheets();
    }

    public function testNewDateInLoadedExcel1900Spreadsheet(): void
    {
        $filename = 'tests/data/Reader/XLS/1900_Calendar.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('A4')->setValue('=DATE(2023,1,1)');
        self::assertEquals(44927, $worksheet->getCell('A4')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNewDateInLoadedExcel1904Spreadsheet(): void
    {
        $filename = 'tests/data/Reader/XLS/1904_Calendar.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('A4')->setValue('=DATE(2023,1,1)');
        self::assertEquals(43465, $worksheet->getCell('A4')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testSwitchCalendars(): void
    {
        $filename1904 = 'tests/data/Reader/XLS/1904_Calendar.xls';
        $reader1904 = new Xls();
        $spreadsheet1904 = $reader1904->load($filename1904);
        $worksheet1904 = $spreadsheet1904->getActiveSheet();
        $date1 = Date::convertIsoDate('2022-01-01');
        self::assertSame(43100.0, $date1);

        $filename1900 = 'tests/data/Reader/XLS/1900_Calendar.xls';
        $reader1900 = new Xls();
        $spreadsheet1900 = $reader1900->load($filename1900);
        $worksheet1900 = $spreadsheet1900->getActiveSheet();
        $date2 = Date::convertIsoDate('2022-01-01');
        self::assertSame(44562.0, $date2);

        self::assertSame(44562, $worksheet1900->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet1900->getCell('A1')->getFormattedValue());
        self::assertSame(44926, $worksheet1900->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet1900->getCell('A2')->getFormattedValue());
        self::assertSame(44561, $worksheet1900->getCell('B1')->getCalculatedValue());
        self::assertSame('2021-12-31', $worksheet1900->getCell('B1')->getFormattedValue());
        self::assertSame(44927, $worksheet1900->getCell('B2')->getCalculatedValue());
        self::assertSame('2023-01-01', $worksheet1900->getCell('B2')->getFormattedValue());

        self::assertSame(43100, $worksheet1904->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet1904->getCell('A1')->getFormattedValue());
        self::assertSame(43464, $worksheet1904->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet1904->getCell('A2')->getFormattedValue());
        self::assertSame(43099, $worksheet1904->getCell('B1')->getCalculatedValue());
        self::assertSame('2021-12-31', $worksheet1904->getCell('B1')->getFormattedValue());
        self::assertSame(43465, $worksheet1904->getCell('B2')->getCalculatedValue());
        self::assertSame('2023-01-01', $worksheet1904->getCell('B2')->getFormattedValue());

        // Check that accessing date values from one spreadsheet doesn't break accessing correct values from another
        self::assertSame(44561, $worksheet1900->getCell('B1')->getCalculatedValue());
        self::assertSame('2021-12-31', $worksheet1900->getCell('B1')->getFormattedValue());
        self::assertSame(44927, $worksheet1900->getCell('B2')->getCalculatedValue());
        self::assertSame('2023-01-01', $worksheet1900->getCell('B2')->getFormattedValue());
        self::assertSame(44562, $worksheet1900->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet1900->getCell('A1')->getFormattedValue());
        self::assertSame(44926, $worksheet1900->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet1900->getCell('A2')->getFormattedValue());

        self::assertSame(43099, $worksheet1904->getCell('B1')->getCalculatedValue());
        self::assertSame('2021-12-31', $worksheet1904->getCell('B1')->getFormattedValue());
        self::assertSame(43465, $worksheet1904->getCell('B2')->getCalculatedValue());
        self::assertSame('2023-01-01', $worksheet1904->getCell('B2')->getFormattedValue());
        self::assertSame(43100, $worksheet1904->getCell('A1')->getValue());
        self::assertSame('2022-01-01', $worksheet1904->getCell('A1')->getFormattedValue());
        self::assertSame(43464, $worksheet1904->getCell('A2')->getValue());
        self::assertSame('2022-12-31', $worksheet1904->getCell('A2')->getFormattedValue());
        $spreadsheet1900->disconnectWorksheets();
        $spreadsheet1904->disconnectWorksheets();
    }
}
