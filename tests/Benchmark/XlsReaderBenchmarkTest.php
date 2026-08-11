<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Benchmarks for the XLS (BIFF8) reader.
 *
 * Run with: vendor/bin/phpunit --testsuite Benchmark --filter XlsReaderBenchmarkTest --stderr
 */
#[Group('benchmark')]
class XlsReaderBenchmarkTest extends TestCase
{
    private const XLS_FIXTURES_DIR = 'tests/data/Reader/XLS';

    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
    }

    /**
     * Benchmark reading a set of real XLS test fixtures.
     *
     * Exercises the full reader path: SST parsing, cell reads with styles,
     * formulas, rich text, conditional formatting, and data validation.
     */
    public function testRealXlsFixtures(): void
    {
        $fixtures = [
            self::XLS_FIXTURES_DIR . '/biff8cover.xls',
            self::XLS_FIXTURES_DIR . '/formulas.xls',
            self::XLS_FIXTURES_DIR . '/RichTextFontSize.xls',
            self::XLS_FIXTURES_DIR . '/Colours.xls',
            self::XLS_FIXTURES_DIR . '/PageSetup.xls',
            self::XLS_FIXTURES_DIR . '/DataValidation.xls',
        ];

        foreach ($fixtures as $fixture) {
            self::assertFileExists($fixture, "Fixture missing: {$fixture}");
        }

        $iterations = 5;
        $reader = new XlsReader();

        // Warm up
        $warmup = $reader->load($fixtures[0]);
        $warmup->disconnectWorksheets();
        unset($warmup);

        gc_collect_cycles();
        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        for ($iter = 0; $iter < $iterations; ++$iter) {
            foreach ($fixtures as $fixture) {
                $spreadsheet = $reader->load($fixture);
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            }
        }

        $end = hrtime(true);
        $memAfter = memory_get_usage(true);
        gc_collect_cycles();

        $totalMs = ($end - $start) / 1_000_000;
        $avgMs = $totalMs / $iterations;
        $memDeltaMb = ($memAfter - $memBefore) / 1024 / 1024;

        fwrite(STDERR, "\n");
        fwrite(STDERR, sprintf("=== XLS Real Fixtures Benchmark (%d files, %d iterations) ===\n", count($fixtures), $iterations));
        fwrite(STDERR, sprintf("  PHP version:   %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  Total:         %.2f ms (%.2f ms avg per iteration)\n", $totalMs, $avgMs));
        fwrite(STDERR, sprintf("  Per file avg:  %.2f ms\n", $avgMs / count($fixtures)));
        fwrite(STDERR, sprintf("  Memory delta:  %.2f MB\n", $memDeltaMb));
        fwrite(STDERR, "\n");

        // Verify the reader still produces valid output
        $spreadsheet = $reader->load($fixtures[0]);
        self::assertGreaterThan(0, $spreadsheet->getSheetCount());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark reading a synthetic XLS file with many cells.
     *
     * Targets per-cell overhead: setXfIndexNoUpdate(), cached read filter,
     * pre-computed cell coordinates.
     */
    public function testSyntheticManyCells(): void
    {
        $xlsFile = $this->createSyntheticXls(
            sheets: 3,
            rowsPerSheet: 500,
            columnsPerSheet: 8,
            withStyles: true,
            withFormulas: true
        );

        $iterations = 5;
        $reader = new XlsReader();

        // Warm up
        $warmup = $reader->load($xlsFile);
        $totalCells = 0;
        foreach ($warmup->getWorksheetIterator() as $sheet) {
            $totalCells += count($sheet->getCellCollection()->getCoordinates());
        }
        $warmup->disconnectWorksheets();
        unset($warmup);

        gc_collect_cycles();
        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        for ($iter = 0; $iter < $iterations; ++$iter) {
            $spreadsheet = $reader->load($xlsFile);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        $end = hrtime(true);
        $memAfter = memory_get_usage(true);
        gc_collect_cycles();

        $totalMs = ($end - $start) / 1_000_000;
        $avgMs = $totalMs / $iterations;
        $memDeltaMb = ($memAfter - $memBefore) / 1024 / 1024;

        fwrite(STDERR, "\n");
        fwrite(STDERR, sprintf("=== XLS Synthetic Many-Cell Benchmark (%d cells, %d iterations) ===\n", $totalCells, $iterations));
        fwrite(STDERR, sprintf("  PHP version:   %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  Total:         %.2f ms (%.2f ms avg per iteration)\n", $totalMs, $avgMs));
        fwrite(STDERR, sprintf("  Memory delta:  %.2f MB\n", $memDeltaMb));
        fwrite(STDERR, "\n");

        // Verify correctness
        $spreadsheet = $reader->load($xlsFile);
        self::assertSame(3, $spreadsheet->getSheetCount());
        $firstSheet = $spreadsheet->getSheet(0);
        self::assertNotNull($firstSheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark reading a synthetic XLS file heavy on shared strings (SST).
     *
     * Targets SST parsing: chunk_split optimization for compressed-to-uncompressed
     * expansion and CONTINUE record handling.
     */
    public function testSyntheticSstHeavy(): void
    {
        $xlsFile = $this->createSstHeavyXls(rows: 1000, columns: 10);

        $iterations = 5;
        $reader = new XlsReader();

        // Warm up
        $warmup = $reader->load($xlsFile);
        $totalCells = 0;
        foreach ($warmup->getWorksheetIterator() as $sheet) {
            $totalCells += count($sheet->getCellCollection()->getCoordinates());
        }
        $warmup->disconnectWorksheets();
        unset($warmup);

        gc_collect_cycles();
        $memBefore = memory_get_usage(true);
        $start = hrtime(true);

        for ($iter = 0; $iter < $iterations; ++$iter) {
            $spreadsheet = $reader->load($xlsFile);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        $end = hrtime(true);
        $memAfter = memory_get_usage(true);
        gc_collect_cycles();

        $totalMs = ($end - $start) / 1_000_000;
        $avgMs = $totalMs / $iterations;
        $memDeltaMb = ($memAfter - $memBefore) / 1024 / 1024;

        fwrite(STDERR, "\n");
        fwrite(STDERR, sprintf("=== XLS SST-Heavy Benchmark (%d string cells, %d iterations) ===\n", $totalCells, $iterations));
        fwrite(STDERR, sprintf("  PHP version:   %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  Total:         %.2f ms (%.2f ms avg per iteration)\n", $totalMs, $avgMs));
        fwrite(STDERR, sprintf("  Memory delta:  %.2f MB\n", $memDeltaMb));
        fwrite(STDERR, "\n");

        // Verify correctness
        $spreadsheet = $reader->load($xlsFile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertIsString($sheet->getCell('A1')->getValue());
        self::assertGreaterThan(0, strlen((string) $sheet->getCell('A1')->getValue()));
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Create a synthetic XLS file with mixed data, styles, and formulas.
     */
    private function createSyntheticXls(
        int $sheets,
        int $rowsPerSheet,
        int $columnsPerSheet,
        bool $withStyles,
        bool $withFormulas,
    ): string {
        $spreadsheet = new Spreadsheet();
        $columns = array_map(
            fn (int $i) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i),
            range(1, $columnsPerSheet)
        );

        for ($s = 0; $s < $sheets; ++$s) {
            $sheet = ($s === 0) ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle("Sheet{$s}");

            for ($row = 1; $row <= $rowsPerSheet; ++$row) {
                foreach ($columns as $colIdx => $col) {
                    if ($withFormulas && $colIdx === $columnsPerSheet - 1 && $row > 1) {
                        // Last column: SUM formula
                        $firstCol = $columns[0];
                        $lastCol = $columns[$columnsPerSheet - 2];
                        $sheet->getCell("{$col}{$row}")
                            ->setValue("=SUM({$firstCol}{$row}:{$lastCol}{$row})");
                    } else {
                        $sheet->getCell("{$col}{$row}")
                            ->setValue($row * ($colIdx + 1) + $s * 1000);
                    }
                }

                if ($withStyles && $row % 5 === 0) {
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF003366']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFD9E2F3'],
                        ],
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                }
            }
        }

        $filename = File::temporaryFilename();
        $this->tempFiles[] = $filename;
        $writer = new XlsWriter($spreadsheet);
        $writer->save($filename);
        $spreadsheet->disconnectWorksheets();

        return $filename;
    }

    /**
     * Create a synthetic XLS file with many distinct string values
     * to exercise SST (Shared String Table) parsing.
     */
    private function createSstHeavyXls(int $rows, int $columns): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $colLetters = array_map(
            fn (int $i) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i),
            range(1, $columns)
        );

        for ($row = 1; $row <= $rows; ++$row) {
            foreach ($colLetters as $colIdx => $col) {
                // Use varied-length strings with enough uniqueness to populate the SST
                $sheet->getCell("{$col}{$row}")
                    ->setValue("StringValue_R{$row}_C{$colIdx}_" . str_repeat('x', ($row + $colIdx) % 20 + 5));
            }
        }

        $filename = File::temporaryFilename();
        $this->tempFiles[] = $filename;
        $writer = new XlsWriter($spreadsheet);
        $writer->save($filename);
        $spreadsheet->disconnectWorksheets();

        return $filename;
    }
}
