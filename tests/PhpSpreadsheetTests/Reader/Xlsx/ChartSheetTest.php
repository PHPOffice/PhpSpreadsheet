<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ChartSheetTest extends TestCase
{
    public function testLoadChartSheetWithCharts(): void
    {
        $filename = 'tests/data/Reader/XLSX/ChartSheet.xlsx';
        $reader = new Xlsx();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($filename);

        self::assertCount(2, $spreadsheet->getAllSheets());
        $chartSheet = $spreadsheet->getSheetByName('Chart1');
        self::assertInstanceOf(Worksheet::class, $chartSheet);
        self::assertSame(1, $chartSheet->getChartCount());
    }

    public function testLoadChartSheetWithoutCharts(): void
    {
        $filename = 'tests/data/Reader/XLSX/ChartSheet.xlsx';
        $reader = new Xlsx();
        $reader->setIncludeCharts(false);
        $spreadsheet = $reader->load($filename);

        self::assertCount(1, $spreadsheet->getAllSheets());
        $chartSheet = $spreadsheet->getSheetByName('Chart1');
        self::assertNull($chartSheet);
    }
}
