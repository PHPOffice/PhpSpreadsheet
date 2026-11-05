<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Benchmark tests for ReferenceHelper's optimized insertNewBefore() method.
 *
 * These tests demonstrate the performance improvement of using indexed cell
 * lookup (getCoordinatesInRange / getCoordinatesOutsideRange) instead of
 * iterating over all cells in the worksheet.
 *
 * Run with: vendor/bin/phpunit --group benchmark
 */
#[\PHPUnit\Framework\Attributes\Group('benchmark')]
class ReferenceHelperBenchmark extends TestCase
{
    private const NUM_ROWS = 500;
    private const NUM_COLS = 20;

    /**
     * Build a worksheet with NUM_ROWS x NUM_COLS cells, including formulas.
     */
    private function buildLargeWorksheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($row = 1; $row <= self::NUM_ROWS; ++$row) {
            for ($col = 1; $col <= self::NUM_COLS; ++$col) {
                $colString = Coordinate::stringFromColumnIndex($col);
                $coord = $colString . $row;

                if ($col > 1 && $row > 1 && $col % 3 === 0) {
                    // Every 3rd column (from col 2+, row 2+) gets a formula
                    $prevCol = Coordinate::stringFromColumnIndex($col - 1);
                    $sheet->setCellValue($coord, "={$prevCol}{$row}*2");
                } elseif ($row > 1 && $col === 1 && $row % 5 === 0) {
                    // Every 5th row in column A gets a SUM formula
                    $startRow = $row - 4;
                    $sheet->setCellValue($coord, "=SUM(A{$startRow}:A" . ($row - 1) . ')');
                } else {
                    $sheet->setCellValue($coord, $row * 100 + $col);
                }
            }
        }

        return $spreadsheet;
    }

    /**
     * Benchmark inserting 10 rows in the middle of a large worksheet.
     */
    #[\PHPUnit\Framework\Attributes\Group('benchmark')]
    public function testInsertRowsPerformance(): void
    {
        $spreadsheet = $this->buildLargeWorksheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellCount = count($sheet->getCellCollection()->getCoordinates());
        fwrite(STDERR, "\n--- Insert Rows Benchmark ---\n");
        fwrite(STDERR, 'Worksheet size: ' . self::NUM_ROWS . ' rows x ' . self::NUM_COLS . " cols = {$cellCount} cells\n");

        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        $sheet->insertNewRowBefore(250, 10);

        $elapsed = (hrtime(true) - $start) / 1_000_000;
        $memAfter = memory_get_usage(true);
        $memDelta = ($memAfter - $memBefore) / 1024 / 1024;

        fwrite(STDERR, sprintf("Insert 10 rows at row 250: %.2f ms\n", $elapsed));
        fwrite(STDERR, sprintf("Memory delta: %.2f MB (before: %.2f MB, after: %.2f MB)\n", $memDelta, $memBefore / 1024 / 1024, $memAfter / 1024 / 1024));
        fwrite(STDERR, sprintf("Peak memory: %.2f MB\n", memory_get_peak_usage(true) / 1024 / 1024));

        // Verify the operation was correct: original cell at A251 should have moved to A261
        // (A251 had value 251*100+1 = 25101, now at row 261)
        self::assertSame(25101, $sheet->getCell('A261')->getValue());

        // Row count should have increased
        $newCellCount = count($sheet->getCellCollection()->getCoordinates());
        fwrite(STDERR, "Cells after insert: {$newCellCount}\n");

        self::assertLessThan(5000, $elapsed, "Insert rows took too long: {$elapsed} ms");

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark inserting 5 columns in the middle of a large worksheet.
     */
    #[\PHPUnit\Framework\Attributes\Group('benchmark')]
    public function testInsertColumnsPerformance(): void
    {
        $spreadsheet = $this->buildLargeWorksheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellCount = count($sheet->getCellCollection()->getCoordinates());
        fwrite(STDERR, "\n--- Insert Columns Benchmark ---\n");
        fwrite(STDERR, 'Worksheet size: ' . self::NUM_ROWS . ' rows x ' . self::NUM_COLS . " cols = {$cellCount} cells\n");

        // Insert 5 columns at column J (column 10)
        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        $sheet->insertNewColumnBefore('J', 5);

        $elapsed = (hrtime(true) - $start) / 1_000_000;
        $memAfter = memory_get_usage(true);
        $memDelta = ($memAfter - $memBefore) / 1024 / 1024;

        fwrite(STDERR, sprintf("Insert 5 columns at column J: %.2f ms\n", $elapsed));
        fwrite(STDERR, sprintf("Memory delta: %.2f MB (before: %.2f MB, after: %.2f MB)\n", $memDelta, $memBefore / 1024 / 1024, $memAfter / 1024 / 1024));
        fwrite(STDERR, sprintf("Peak memory: %.2f MB\n", memory_get_peak_usage(true) / 1024 / 1024));

        // Verify: original column J (10) data should now be at column O (15)
        // Cell J1 had value 1*100+10 = 110, now at O1
        self::assertSame(110, $sheet->getCell('O1')->getValue());

        $newCellCount = count($sheet->getCellCollection()->getCoordinates());
        fwrite(STDERR, "Cells after insert: {$newCellCount}\n");

        self::assertLessThan(5000, $elapsed, "Insert columns took too long: {$elapsed} ms");

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark getCoordinatesInRange() on a large cell collection.
     *
     * This tests the indexed filtering directly, which is the core of the
     * performance optimization in ReferenceHelper::insertNewBefore().
     */
    #[\PHPUnit\Framework\Attributes\Group('benchmark')]
    public function testGetCoordinatesInRangePerformance(): void
    {
        $spreadsheet = $this->buildLargeWorksheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCollection = $sheet->getCellCollection();

        $totalCoords = count($cellCollection->getCoordinates());
        fwrite(STDERR, "\n--- getCoordinatesInRange Benchmark ---\n");
        fwrite(STDERR, "Total cells in collection: {$totalCoords}\n");

        // Benchmark: get cells in range from row 250, col 10 onward
        $start = hrtime(true);

        $inRange = $cellCollection->getCoordinatesInRange(250, 10);

        $elapsedInRange = (hrtime(true) - $start) / 1_000_000;

        fwrite(STDERR, sprintf("getCoordinatesInRange(250, 10): %.2f ms, returned %d cells\n", $elapsedInRange, count($inRange)));

        // Benchmark: get cells outside that range
        $start = hrtime(true);

        $outsideRange = $cellCollection->getCoordinatesOutsideRange(250, 10);

        $elapsedOutside = (hrtime(true) - $start) / 1_000_000;

        fwrite(STDERR, sprintf("getCoordinatesOutsideRange(250, 10): %.2f ms, returned %d cells\n", $elapsedOutside, count($outsideRange)));

        // The two sets should cover all cells (they are complements)
        self::assertSame($totalCoords, count($inRange) + count($outsideRange));

        // Verify that all returned "in range" coordinates are indeed >= row 250 and >= col 10
        foreach ($inRange as $coord) {
            [$col, $row] = Coordinate::indexesFromString($coord);
            self::assertGreaterThanOrEqual(250, $row, "Cell {$coord} row should be >= 250");
            self::assertGreaterThanOrEqual(10, $col, "Cell {$coord} col should be >= 10");
        }

        // Benchmark: get cells from row 1, col 1 (should return all cells)
        $start = hrtime(true);

        $allCells = $cellCollection->getCoordinatesInRange(1, 1);

        $elapsedAll = (hrtime(true) - $start) / 1_000_000;

        fwrite(STDERR, sprintf("getCoordinatesInRange(1, 1) [all cells]: %.2f ms, returned %d cells\n", $elapsedAll, count($allCells)));
        self::assertCount($totalCoords, $allCells);

        // Benchmark: get cells from last row (should return very few)
        $start = hrtime(true);

        $lastRowCells = $cellCollection->getCoordinatesInRange(self::NUM_ROWS, 1);

        $elapsedLast = (hrtime(true) - $start) / 1_000_000;

        fwrite(STDERR, sprintf("getCoordinatesInRange(%d, 1) [last row]: %.2f ms, returned %d cells\n", self::NUM_ROWS, $elapsedLast, count($lastRowCells)));
        self::assertCount(self::NUM_COLS, $lastRowCells);

        fwrite(STDERR, "---\n");

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark: compare getCoordinates() (full iteration) vs getCoordinatesInRange() (indexed).
     */
    #[\PHPUnit\Framework\Attributes\Group('benchmark')]
    public function testIndexedVsFullIterationComparison(): void
    {
        $spreadsheet = $this->buildLargeWorksheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCollection = $sheet->getCellCollection();

        fwrite(STDERR, "\n--- Indexed vs Full Iteration Comparison ---\n");

        // Full iteration: get all coordinates then filter manually
        $start = hrtime(true);
        $allCoords = $cellCollection->getCoordinates();
        $filteredManually = [];
        foreach ($allCoords as $coord) {
            [$col, $row] = Coordinate::indexesFromString($coord);
            if ($row >= 250 && $col >= 10) {
                $filteredManually[] = $coord;
            }
        }
        $elapsedManual = (hrtime(true) - $start) / 1_000_000;

        // Indexed lookup
        $start = hrtime(true);
        $filteredIndexed = $cellCollection->getCoordinatesInRange(250, 10);
        $elapsedIndexed = (hrtime(true) - $start) / 1_000_000;

        fwrite(STDERR, sprintf("Manual filter (getCoordinates + loop): %.2f ms, found %d cells\n", $elapsedManual, count($filteredManually)));
        fwrite(STDERR, sprintf("Indexed lookup (getCoordinatesInRange): %.2f ms, found %d cells\n", $elapsedIndexed, count($filteredIndexed)));

        if ($elapsedManual > 0) {
            fwrite(STDERR, sprintf("Speedup factor: %.1fx\n", $elapsedManual / max($elapsedIndexed, 0.001)));
        }

        // Results should match
        sort($filteredManually);
        sort($filteredIndexed);
        self::assertSame($filteredManually, $filteredIndexed);

        $spreadsheet->disconnectWorksheets();
    }
}
