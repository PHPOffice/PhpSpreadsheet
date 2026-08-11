<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class PageBreakTest extends TestCase
{
    public function testRows(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'First Page');
        $sheet->setCellValue('B2', 'Second Page');

        $sheet->getPageSetup()->setPrintArea('B1:B2');
        $sheet->setBreak('B1', Worksheet::BREAK_ROW);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<rowBreaks count="1" manualBreakCount="1"><brk id="1" man="1" max="2"/></rowBreaks>', $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testRowsNoPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'First Page');
        $sheet->setCellValue('B2', 'Second Page');

        $sheet->setBreak('B1', Worksheet::BREAK_ROW);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<rowBreaks count="1" manualBreakCount="1"><brk id="1" man="1"/></rowBreaks>', $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCols(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'First Page');
        $sheet->setCellValue('C1', 'Second Page');

        $sheet->getPageSetup()->setPrintArea('B1:C1');
        $sheet->setBreak('C1', Worksheet::BREAK_COLUMN);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<colBreaks count="1" manualBreakCount="1"><brk id="2" man="1" max="1"/></colBreaks>', $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testColsNoPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'First Page');
        $sheet->setCellValue('C1', 'Second Page');

        $sheet->setBreak('C1', Worksheet::BREAK_COLUMN);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<colBreaks count="1" manualBreakCount="1"><brk id="2" man="1"/></colBreaks>', $data);
        $spreadsheet->disconnectWorksheets();
    }
}
