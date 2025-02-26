<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue3982Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3982.xlsx';

    public function testLoadAllRows(): void
    {
        if (!method_exists(TestCase::class, 'setOutputCallback')) {
            self::markTestSkipped('Memory loop in Phpunit 10');
        }
        $spreadsheet = IOFactory::load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, false, true);
        self::assertCount(1048576, $data);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIgnoreCellsWithNoRows(): void
    {
        $spreadsheet = IOFactory::load(self::$testbook, IReader::IGNORE_ROWS_WITH_NO_CELLS);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, false, true);
        self::assertSame([1, 2, 3, 4, 5, 6], array_keys($data));
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefaultSetting(): void
    {
        $reader = new XlsxReader();
        self::assertFalse($reader->getIgnoreRowsWithNoCells());
        self::assertFalse($reader->getReadDataOnly());
        self::assertFalse($reader->getIncludeCharts());
        self::assertTrue($reader->getReadEmptyCells());
    }
}
