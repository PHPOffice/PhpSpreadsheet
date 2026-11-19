<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class FormattedValueTest extends TestCase
{
    public function testFormattedTextUsesSpreadsheetCalendar(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setExcelCalendar(Date::CALENDAR_MAC_1904);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2.5);
        $sheet->getStyle('A1')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet->getStyle('A2')->getNumberFormat()->setFormatCode('0.00');
        $expected1 = $sheet->getCell('A1')->getFormattedValue();
        $expected2 = $sheet->getCell('A2')->getFormattedValue();
        $sheet->setSelectedCells('B3');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();

        self::assertSame('1904-01-02', $expected1);
        self::assertStringContainsString("<text:p>$expected1</text:p>", $data);
        self::assertStringContainsString("<text:p>$expected2</text:p>", $data);
        self::assertSame(Date::CALENDAR_WINDOWS_1900, Date::getExcelCalendar());
        self::assertSame('B3', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }
}
