<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Benchmark populate + Xlsx save for a large dense sheet.
 *
 * Run with: vendor/bin/phpunit --testsuite Benchmark --filter LargeXlsxWriteBenchmark --stderr
 */
#[Group('benchmark')]
class LargeXlsxWriteBenchmarkTest extends TestCase
{
    private const ROWS = 2000;

    private const COLS = 20;

    public function testPopulateAndSaveLargeSheet(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'phpspreadsheet-bench-');
        self::assertNotFalse($tmp);
        $filename = $tmp . '.xlsx';
        @unlink($tmp);

        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= self::ROWS; ++$row) {
            for ($col = 1; $col <= self::COLS; ++$col) {
                $sheet->getCell([$col, $row])->setValue("R{$row}C{$col}");
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($filename);

        $elapsedMs = (hrtime(true) - $start) / 1e6;
        $peakMiB = memory_get_peak_usage(true) / (1024 * 1024);
        $deltaMiB = (memory_get_usage(true) - $memBefore) / (1024 * 1024);
        $cellCount = self::ROWS * self::COLS;

        fwrite(
            STDERR,
            sprintf(
                "LargeXlsxWriteBenchmark: %d cells, %.1f ms, peak=%.1f MiB, delta=%.1f MiB, size=%.1f KiB\n",
                $cellCount,
                $elapsedMs,
                $peakMiB,
                $deltaMiB,
                filesize($filename) / 1024
            )
        );

        self::assertFileExists($filename);
        self::assertGreaterThan(1000, filesize($filename));

        $spreadsheet->disconnectWorksheets();
        @unlink($filename);
    }
}
