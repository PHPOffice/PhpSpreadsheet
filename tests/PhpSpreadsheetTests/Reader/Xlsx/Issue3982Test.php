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

    /**
     * This routine comes nowhere close to out-of-memory (uses 45MB).
     * Yet it goes out of memory in PhpUnit 10 (uses 2GB!).
     * Works fine in PhpUnit9-.
     * We can mitigate the problem by changing entirely-null rows
     * to empty rows in rangeToArrayYieldRows. (uses 455MB).
     * That's a breaking change, but might be worth considering.
     *
     * Aha! I have narrowed the problem to self::assertCount,
     * which has a ready substitution. I will report the problem
     * to Phpunit if I can come up with a simpler example.
     */
    public function testLoadAllRows(): void
    {
        $spreadsheet = IOFactory::load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, false, true);
        $count = count($data);
        self::assertSame(1_048_576, $count);
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
