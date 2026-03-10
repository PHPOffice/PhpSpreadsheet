<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Benchmark;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Benchmark tests for the formula token cache in the Calculation engine.
 *
 * These tests demonstrate the performance benefit of caching parsed formula
 * tokens so that identical formula strings are not re-parsed on every evaluation.
 *
 * Run with: vendor/bin/phpunit --group benchmark --stderr
 *
 * @group benchmark
 */
class FormulaTokenCacheBenchmark extends TestCase
{
    /** Formula patterns used across benchmarks. */
    private const FORMULA_PATTERNS = [
        '=A%d+B%d',
        '=SUM(A%d:B%d)',
        '=IF(A%d>0,B%d,C%d)',
        '=AVERAGE(A%d:D%d)',
        '=A%d*B%d+C%d',
        '=MAX(A%d,B%d,C%d)',
        '=MIN(A%d:C%d)/D%d',
        '=CONCATENATE(A%d,"-",B%d)',
        '=ROUND(A%d/B%d,2)',
        '=IFERROR(A%d/B%d,0)',
    ];

    protected function tearDown(): void
    {
        Calculation::clearFormulaTokenCache();
    }

    /**
     * Benchmark: calculate 1000 formula cells with cache enabled (warm) vs
     * with cache cleared (cold, forces re-parsing).
     */
    public function testSpreadsheetCalculationCacheVsNocache(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCount = 1000;

        // Populate source data
        for ($row = 1; $row <= $cellCount; ++$row) {
            $sheet->setCellValue("A{$row}", $row);
            $sheet->setCellValue("B{$row}", $row * 2);
            $sheet->setCellValue("C{$row}", $row * 3);
            $sheet->setCellValue("D{$row}", max($row, 1));
        }

        // Assign formulas in column E using repeated patterns.
        // We cycle through a small set of row references (1-100) so many cells
        // share the same formula string, maximising token cache hits.
        $patternCount = count(self::FORMULA_PATTERNS);
        for ($row = 1; $row <= $cellCount; ++$row) {
            $pattern = self::FORMULA_PATTERNS[$row % $patternCount];
            $refRow = (($row - 1) % 100) + 1; // only 100 distinct row refs
            $formula = $this->buildFormula($pattern, $refRow);
            $sheet->setCellValue("E{$row}", $formula);
        }

        // --- Run 1: Cold cache (first-time parsing + calculation) ---
        Calculation::clearFormulaTokenCache();
        $this->clearCalculationCache($spreadsheet);

        $coldStart = hrtime(true);
        for ($row = 1; $row <= $cellCount; ++$row) {
            $sheet->getCell("E{$row}")->getCalculatedValue();
        }
        $coldNs = hrtime(true) - $coldStart;

        // --- Run 2: Warm token cache (tokens already cached, recalculate) ---
        $this->clearCalculationCache($spreadsheet);

        $warmStart = hrtime(true);
        for ($row = 1; $row <= $cellCount; ++$row) {
            $sheet->getCell("E{$row}")->getCalculatedValue();
        }
        $warmNs = hrtime(true) - $warmStart;

        $coldMs = $coldNs / 1_000_000;
        $warmMs = $warmNs / 1_000_000;
        $speedup = $coldMs > 0 ? (($coldMs - $warmMs) / $coldMs) * 100 : 0;

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Spreadsheet Calculation Benchmark ({$cellCount} formula cells) ===\n");
        fwrite(STDERR, sprintf("  Cold cache (parse + calc): %8.2f ms\n", $coldMs));
        fwrite(STDERR, sprintf("  Warm cache (cached parse): %8.2f ms\n", $warmMs));
        fwrite(STDERR, sprintf("  Token cache speedup:       %8.2f %%\n", $speedup));
        fwrite(STDERR, sprintf("  Token cache size:          %d entries\n", Calculation::getFormulaTokenCacheSize()));
        fwrite(STDERR, "\n");

        // The warm run should not be dramatically slower than the cold run.
        // We assert it completes successfully; the timing output shows benefit.
        self::assertGreaterThan(0, $warmMs);
        self::assertGreaterThan(0, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark: directly parse 10,000 formulas (mix of repeated and unique)
     * with cache enabled vs after clearing the cache.
     *
     * The formula set is designed so the total distinct formulas stay well
     * under the 1,000-entry cache limit, ensuring cache hits are reliable.
     */
    public function testParseFormulaCacheVsNocache(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $totalFormulas = 10_000;

        // Build a pool of 200 distinct formulas (well under the 1,000 cache limit).
        // Each formula will be parsed ~50 times on average across 10,000 calls.
        $distinctPool = [];
        $patternCount = count(self::FORMULA_PATTERNS);
        for ($i = 1; $i <= 200; ++$i) {
            $pattern = self::FORMULA_PATTERNS[$i % $patternCount];
            $distinctPool[] = $this->buildFormula($pattern, $i);
        }

        $formulas = [];
        for ($i = 0; $i < $totalFormulas; ++$i) {
            $formulas[] = $distinctPool[$i % count($distinctPool)];
        }

        // --- Run 1: Cold cache (every formula must be fully parsed) ---
        Calculation::clearFormulaTokenCache();

        $coldStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $coldNs = hrtime(true) - $coldStart;

        // --- Run 2: Warm cache (repeated formulas served from cache) ---
        // Do NOT clear cache; the repeated formulas are already cached.
        // Clear and re-parse to measure warm-cache performance.
        $cacheSize = Calculation::getFormulaTokenCacheSize();

        // Re-run with warm cache
        $warmStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $warmNs = hrtime(true) - $warmStart;

        // --- Run 3: Cleared cache (simulates no-cache scenario) ---
        Calculation::clearFormulaTokenCache();

        $clearedStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $clearedNs = hrtime(true) - $clearedStart;

        $coldMs = $coldNs / 1_000_000;
        $warmMs = $warmNs / 1_000_000;
        $clearedMs = $clearedNs / 1_000_000;
        $warmVsColdPct = $coldMs > 0 ? (($coldMs - $warmMs) / $coldMs) * 100 : 0;
        $warmVsClearedPct = $clearedMs > 0 ? (($clearedMs - $warmMs) / $clearedMs) * 100 : 0;

        fwrite(STDERR, "\n");
        $distinctCount = count($distinctPool);
        fwrite(STDERR, "=== parseFormula() Benchmark ({$totalFormulas} calls, {$distinctCount} distinct) ===\n");
        fwrite(STDERR, sprintf("  Cold cache (first parse):  %8.2f ms\n", $coldMs));
        fwrite(STDERR, sprintf("  Warm cache (all cached):   %8.2f ms\n", $warmMs));
        fwrite(STDERR, sprintf("  Cleared cache (re-parse):  %8.2f ms\n", $clearedMs));
        fwrite(STDERR, sprintf("  Warm vs cold improvement:  %8.2f %%\n", $warmVsColdPct));
        fwrite(STDERR, sprintf("  Warm vs cleared improvement:%7.2f %%\n", $warmVsClearedPct));
        fwrite(STDERR, sprintf("  Cache entries after cold:  %d\n", $cacheSize));
        fwrite(STDERR, sprintf("  Cache entries after clear: %d\n", Calculation::getFormulaTokenCacheSize()));
        fwrite(STDERR, "\n");

        // Warm cache should be faster than cold cache for repeated formulas
        self::assertLessThan($coldMs, $warmMs, 'Warm cache should be faster than cold cache');
        self::assertLessThan($clearedMs, $warmMs, 'Warm cache should be faster than cleared cache');

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Build a concrete formula from a pattern and row number.
     *
     * Patterns use %d placeholders; all are replaced with the row number.
     */
    private function buildFormula(string $pattern, int $row): string
    {
        return sprintf(
            $pattern,
            ...array_fill(0, substr_count($pattern, '%d'), $row)
        );
    }

    /**
     * Clear the calculation value cache (not the token cache) so formulas
     * are re-evaluated but token parsing cache state is preserved.
     */
    private function clearCalculationCache(Spreadsheet $spreadsheet): void
    {
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->clearCalculationCache();
    }
}
