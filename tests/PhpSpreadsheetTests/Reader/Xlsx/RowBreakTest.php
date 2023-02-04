<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class RowBreakTest extends TestCase
{
    public function testReadAndWriteRowBreak(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.3143a.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);
        $expected = '<rowBreaks count="1" manualBreakCount="1"><brk id="25" man="1" max="16383"/></rowBreaks>';
        self::assertStringContainsString($expected, $data);
        $spreadsheet->disconnectWorksheets();
    }
}
