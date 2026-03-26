<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Benchmark tests for the formula token cache in the Calculation engine.
 *
 * These tests demonstrate the performance benefit of caching parsed formula
 * tokens so that identical formula strings are not re-parsed on every evaluation.
 *
 * Run with: vendor/bin/phpunit --testsuite Benchmark --filter FormulaTokenCacheBenchmark --stderr
 */
#[\PHPUnit\Framework\Attributes\Group('benchmark')]
class FormulaTokenCacheBenchmarkTest extends TestCase
{
    private const CACHE_SIZE = 1000;

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

    /**
     * Benchmark: parse 1000 spreadsheet-like formulas with cache enabled vs disabled.
     */
    public function testParseFormulaCacheEnabledVsDisabled(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $cellCount = 1000;

        // Build a realistic set of formulas that a spreadsheet might contain
        $patternCount = count(self::FORMULA_PATTERNS);
        $formulas = [];
        for ($row = 1; $row <= $cellCount; ++$row) {
            $pattern = self::FORMULA_PATTERNS[$row % $patternCount];
            $refRow = (($row - 1) % 100) + 1;
            $formulas[] = $this->buildFormula($pattern, $refRow);
        }

        // --- Run 1: Cache disabled (default) ---
        $calculation->setFormulaTokenCacheMaxSize(0);

        $noCacheStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $noCacheNs = hrtime(true) - $noCacheStart;

        // --- Run 2: Cache enabled, cold ---
        $calculation->setFormulaTokenCacheMaxSize(self::CACHE_SIZE);

        $coldStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $coldNs = hrtime(true) - $coldStart;
        $cacheSize = $calculation->getFormulaTokenCacheSize();

        // --- Run 3: Cache enabled, warm ---
        $warmStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $warmNs = hrtime(true) - $warmStart;

        $noCacheMs = $noCacheNs / 1_000_000;
        $coldMs = $coldNs / 1_000_000;
        $warmMs = $warmNs / 1_000_000;

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== parseFormula() Enabled vs Disabled ({$cellCount} formulas) ===\n");
        fwrite(STDERR, sprintf("  PHP version:               %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  Cache disabled:            %8.2f ms\n", $noCacheMs));
        fwrite(STDERR, sprintf("  Cache enabled (cold):      %8.2f ms\n", $coldMs));
        fwrite(STDERR, sprintf("  Cache enabled (warm):      %8.2f ms\n", $warmMs));
        fwrite(STDERR, sprintf("  Cache entries:             %d\n", $cacheSize));
        fwrite(STDERR, "\n");

        self::assertGreaterThan(0, $cacheSize);
        self::assertLessThan($noCacheMs, $warmMs, 'Warm cache should be faster than no cache');

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark: directly parse 10,000 formulas (mix of repeated and unique)
     * with cache enabled vs after clearing the cache.
     *
     * The formula set is designed so the total distinct formulas stay well
     * under the cache limit, ensuring cache hits are reliable.
     */
    public function testParseFormulaCacheVsNocache(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setFormulaTokenCacheMaxSize(self::CACHE_SIZE);
        $totalFormulas = 10_000;

        // Build a pool of 200 distinct formulas (well under the cache limit).
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
        $calculation->clearFormulaTokenCache();

        $coldStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $coldNs = hrtime(true) - $coldStart;

        // --- Run 2: Warm cache (repeated formulas served from cache) ---
        $cacheSize = $calculation->getFormulaTokenCacheSize();

        $warmStart = hrtime(true);
        foreach ($formulas as $formula) {
            $calculation->parseFormula($formula);
        }
        $warmNs = hrtime(true) - $warmStart;

        // --- Run 3: Cleared cache (simulates re-parsing) ---
        $calculation->clearFormulaTokenCache();

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
        fwrite(STDERR, sprintf("  PHP version:                %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  Cold cache (first parse):  %8.2f ms\n", $coldMs));
        fwrite(STDERR, sprintf("  Warm cache (all cached):   %8.2f ms\n", $warmMs));
        fwrite(STDERR, sprintf("  Cleared cache (re-parse):  %8.2f ms\n", $clearedMs));
        fwrite(STDERR, sprintf("  Warm vs cold improvement:  %8.2f %%\n", $warmVsColdPct));
        fwrite(STDERR, sprintf("  Warm vs cleared improvement:%7.2f %%\n", $warmVsClearedPct));
        fwrite(STDERR, sprintf("  Cache entries after cold:  %d\n", $cacheSize));
        fwrite(STDERR, sprintf("  Cache entries after clear: %d\n", $calculation->getFormulaTokenCacheSize()));
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
}
