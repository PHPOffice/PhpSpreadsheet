<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;

class StreamingDateTest extends TestCase
{
    private string $file = '';

    protected function tearDown(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testDateConversionUsesWorkbookCalendar(): void
    {
        $this->file = File::temporaryFilename();
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Dates');
        $date = new DateTimeImmutable('2026-01-02 03:04:05');
        $sheet->appendRow([$date]);
        self::assertSame(Date::CALENDAR_MAC_1904, Date::getExcelCalendar());
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        $sheet->appendRow([$date]);
        $writer->close();

        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(Date::CALENDAR_WINDOWS_1900, $spreadsheet->getExcelCalendar());
        self::assertEqualsWithDelta(46024.12783564815, $spreadsheet->getActiveSheet()->getCell('A1')->getValue(), 1E-8);
        self::assertSame(
            $spreadsheet->getActiveSheet()->getCell('A1')->getValue(),
            $spreadsheet->getActiveSheet()->getCell('A2')->getValue()
        );
        $spreadsheet->disconnectWorksheets();
    }
}
