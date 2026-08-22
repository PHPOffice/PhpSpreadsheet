<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class NumberFormatSystemDateTimeTest extends TestCase
{
    private string $shortDateFormat;

    private string $longDateFormat;

    private string $dateTimeFormat;

    private string $timeFormat;

    protected function setUp(): void
    {
        $this->shortDateFormat = NumberFormat::getShortDateFormat();
        $this->longDateFormat = NumberFormat::getLongDateFormat();
        $this->dateTimeFormat = NumberFormat::getDateTimeFormat();
        $this->timeFormat = NumberFormat::getTimeFormat();
    }

    protected function tearDown(): void
    {
        NumberFormat::setShortDateFormat($this->shortDateFormat);
        NumberFormat::setLongDateFormat($this->longDateFormat);
        NumberFormat::setDateTimeFormat($this->dateTimeFormat);
        NumberFormat::setTimeFormat($this->timeFormat);
    }

    public function testOverrides(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $formula = '=DATEVALUE("2024-02-29")+TIMEVALUE("8:12:15 AM")';
        $sheet->getCell('A1')->setValue($formula);
        $sheet->getCell('A2')->setValue($formula);
        $sheet->getStyle('A2')->getNumberFormat()
            ->setBuiltinFormatCode(14);
        $sheet->getCell('A3')->setValue($formula);
        $sheet->getStyle('A3')->getNumberFormat()
            ->setBuiltinFormatCode(15);
        $sheet->getCell('A4')->setValue($formula);
        $sheet->getStyle('A4')->getNumberFormat()
            ->setBuiltinFormatCode(22);
        $sheet->getCell('A5')->setValue($formula);
        $sheet->getStyle('A5')->getNumberFormat()
            ->setFormatCode('[$-F800]');
        $sheet->getCell('A6')->setValue($formula);
        $sheet->getStyle('A6')->getNumberFormat()
            ->setFormatCode('[$-F400]');
        $sheet->getCell('A7')->setValue($formula);
        $sheet->getStyle('A7')->getNumberFormat()
            ->setFormatCode('[$-x-sysdate]');
        $sheet->getCell('A8')->setValue($formula);
        $sheet->getStyle('A8')->getNumberFormat()
            ->setFormatCode('[$-x-systime]');
        $sheet->getCell('A9')->setValue($formula);
        $sheet->getStyle('A9')->getNumberFormat()
            ->setFormatCode('hello' . NumberFormat::FORMAT_SYSDATE_F800 . 'goodbye');
        NumberFormat::setShortDateFormat('yyyy/mm/dd');
        NumberFormat::setDateTimeFormat('yyyy/mm/dd hh:mm AM/PM');
        NumberFormat::setLongDateFormat('dddd d mmm yyyy');
        NumberFormat::setTimeFormat('h:mm');
        self::assertSame('2024/02/29', $sheet->getCell('A2')->getformattedValue());
        self::assertSame('2024/02/29 08:12 AM', $sheet->getCell('A4')->getformattedValue());
        self::assertSame('Thursday 29 Feb 2024', $sheet->getCell('A5')->getformattedValue());
        self::assertSame('8:12', $sheet->getCell('A6')->getformattedValue());
        self::assertSame('Thursday 29 Feb 2024', $sheet->getCell('A7')->getformattedValue());
        self::assertSame('8:12', $sheet->getCell('A8')->getformattedValue());
        self::assertSame('Thursday 29 Feb 2024', $sheet->getCell('A9')->getformattedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefaults(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $formula = '=DATEVALUE("2024-02-29")+TIMEVALUE("8:12:15 AM")';
        $sheet->getCell('A1')->setValue($formula);
        $sheet->getCell('A2')->setValue($formula);
        $sheet->getStyle('A2')->getNumberFormat()
            ->setBuiltinFormatCode(14);
        $sheet->getCell('A3')->setValue($formula);
        $sheet->getStyle('A3')->getNumberFormat()
            ->setBuiltinFormatCode(15);
        $sheet->getCell('A4')->setValue($formula);
        $sheet->getStyle('A4')->getNumberFormat()
            ->setBuiltinFormatCode(22);
        $sheet->getCell('A5')->setValue($formula);
        $sheet->getStyle('A5')->getNumberFormat()
            ->setFormatCode('[$-F800]');
        $sheet->getCell('A6')->setValue($formula);
        $sheet->getStyle('A6')->getNumberFormat()
            ->setFormatCode('[$-F400]');
        $sheet->getCell('A7')->setValue($formula);
        $sheet->getStyle('A7')->getNumberFormat()
            ->setFormatCode('[$-x-sysdate]');
        $sheet->getCell('A8')->setValue($formula);
        $sheet->getStyle('A8')->getNumberFormat()
            ->setFormatCode('[$-x-systime]');
        $sheet->getCell('A9')->setValue($formula);
        $sheet->getStyle('A9')->getNumberFormat()
            ->setFormatCode('hello' . NumberFormat::FORMAT_SYSDATE_F800 . 'goodbye');
        self::assertSame('2/29/2024', $sheet->getCell('A2')->getformattedValue());
        self::assertSame('2/29/2024 8:12', $sheet->getCell('A4')->getformattedValue());
        self::assertSame('Thursday, February 29, 2024', $sheet->getCell('A5')->getformattedValue());
        self::assertSame('8:12:15 AM', $sheet->getCell('A6')->getformattedValue());
        self::assertSame('Thursday, February 29, 2024', $sheet->getCell('A7')->getformattedValue());
        self::assertSame('8:12:15 AM', $sheet->getCell('A8')->getformattedValue());
        self::assertSame('Thursday, February 29, 2024', $sheet->getCell('A9')->getformattedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
