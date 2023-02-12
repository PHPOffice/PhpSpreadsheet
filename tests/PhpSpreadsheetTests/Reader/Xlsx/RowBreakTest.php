<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class RowBreakTest extends TestCase
{
    public function testReadAndWriteRowBreak(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.3143a.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        $expected = '<rowBreaks count="1" manualBreakCount="1"><brk id="25" man="1" max="16383"/></rowBreaks>';
        self::assertStringContainsString($expected, $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteRowBreakInPrintAreaWithMax(): void
    {
        // This test specifies max for setBreak and appears correct.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row < 60; ++$row) {
            for ($column = 'A'; $column !== 'L'; ++$column) {
                $cell = $column . $row;
                $sheet->getCell($cell)->setValue($cell);
            }
        }
        $sheet->getPageSetup()->setPrintArea('B2:J55');
        $sheet->setBreak('A25', Worksheet::BREAK_ROW, Worksheet::BREAK_ROW_MAX_COLUMN);
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        $expected = '<rowBreaks count="1" manualBreakCount="1"><brk id="25" man="1" max="16383"/></rowBreaks>';
        self::assertStringContainsString($expected, $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteRowBreakInPrintAreaWithoutMax(): void
    {
        // This test does not specify max for setBreak,
        // and appears incorrect. Probable Excel bug.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row < 60; ++$row) {
            for ($column = 'A'; $column !== 'L'; ++$column) {
                $cell = $column . $row;
                $sheet->getCell($cell)->setValue($cell);
            }
        }
        $sheet->getPageSetup()->setPrintArea('B2:J55');
        $sheet->setBreak('A25', Worksheet::BREAK_ROW);
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        $expected = '<rowBreaks count="1" manualBreakCount="1"><brk id="25" man="1"/></rowBreaks>';
        self::assertStringContainsString($expected, $data);
        $spreadsheet->disconnectWorksheets();
    }
}
