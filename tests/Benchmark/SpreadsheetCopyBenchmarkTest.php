<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('benchmark')]
class SpreadsheetCopyBenchmarkTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    /**
     * Build a non-trivial spreadsheet with 1000+ cells across multiple sheets,
     * including data values, formulas, and styles.
     */
    private function createPopulatedSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Data values with styles
        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle('Data');

        $columns = ['A', 'B', 'C', 'D', 'E'];
        for ($row = 1; $row <= 200; ++$row) {
            foreach ($columns as $colIdx => $col) {
                $dataSheet->getCell("{$col}{$row}")
                    ->setValue("Data R{$row}C" . ($colIdx + 1));
            }
            // Apply styles to first column
            $dataSheet->getStyle("A{$row}")->applyFromArray([
                'font' => [
                    'bold' => $row % 2 === 0,
                    'color' => ['argb' => 'FF003366'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $row % 3 === 0 ? 'FFFFF2CC' : 'FFD9E2F3'],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
        }

        // Sheet 2: Numeric data
        $numericSheet = $spreadsheet->createSheet();
        $numericSheet->setTitle('Numbers');

        for ($row = 1; $row <= 200; ++$row) {
            foreach ($columns as $colIdx => $col) {
                $numericSheet->getCell("{$col}{$row}")
                    ->setValue($row * ($colIdx + 1) * 1.5);
            }
        }

        // Sheet 3: Formulas referencing Sheet 2
        $formulaSheet = $spreadsheet->createSheet();
        $formulaSheet->setTitle('Formulas');

        for ($row = 1; $row <= 200; ++$row) {
            $formulaSheet->getCell("A{$row}")
                ->setValue("=Numbers!A{$row}+Numbers!B{$row}");
            $formulaSheet->getCell("B{$row}")
                ->setValue("=Numbers!C{$row}*2");
            $formulaSheet->getCell("C{$row}")
                ->setValue("=SUM(Numbers!A{$row}:Numbers!E{$row})");
        }

        // Sheet 4: Mixed content with column widths
        $mixedSheet = $spreadsheet->createSheet();
        $mixedSheet->setTitle('Mixed');

        for ($row = 1; $row <= 100; ++$row) {
            $mixedSheet->getCell("A{$row}")->setValue("Label {$row}");
            $mixedSheet->getCell("B{$row}")->setValue($row * 100);
            $mixedSheet->getCell("C{$row}")->setValue($row % 2 === 0);
            $mixedSheet->getCell("D{$row}")->setValue(date('Y-m-d', strtotime("2024-01-01 +{$row} days")));
        }

        $mixedSheet->getColumnDimension('A')->setWidth(20);
        $mixedSheet->getColumnDimension('B')->setWidth(15);

        return $spreadsheet;
    }

    /**
     * Compare clone vs serialize/unserialize for copying a Spreadsheet.
     *
     * Performance varies across PHP versions and platforms — run this
     * benchmark in your own environment to determine which is faster:
     *
     *   vendor/bin/phpunit --group benchmark --filter SpreadsheetCopyBenchmarkTest --stderr
     */
    public function testCloneVsSerializeBenchmark(): void
    {
        $this->spreadsheet = $this->createPopulatedSpreadsheet();

        // Count total cells to confirm we have 1000+
        $totalCells = 0;
        foreach ($this->spreadsheet->getWorksheetIterator() as $sheet) {
            $totalCells += count($sheet->getCellCollection()->getCoordinates());
        }
        self::assertGreaterThanOrEqual(1000, $totalCells, 'Spreadsheet must have at least 1000 cells');

        // Warm up (first run may include autoloading overhead)
        $warmup = clone $this->spreadsheet;
        $warmup->disconnectWorksheets();
        unset($warmup);
        /** @var Spreadsheet $warmup */
        $warmup = unserialize(serialize($this->spreadsheet));
        $warmup->disconnectWorksheets();
        unset($warmup);

        $iterations = 5;

        // Benchmark clone
        gc_collect_cycles();
        $cloneMemBefore = memory_get_usage(true);
        $cloneStart = hrtime(true);
        $clones = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $clones[] = clone $this->spreadsheet;
        }
        $cloneEnd = hrtime(true);
        $cloneMemAfter = memory_get_usage(true);
        foreach ($clones as $c) {
            $c->disconnectWorksheets();
        }
        unset($clones);
        gc_collect_cycles();

        $cloneTimeMs = ($cloneEnd - $cloneStart) / 1_000_000;
        $cloneAvgMs = $cloneTimeMs / $iterations;
        $cloneMemDeltaMb = ($cloneMemAfter - $cloneMemBefore) / 1024 / 1024;

        // Benchmark serialize/unserialize
        gc_collect_cycles();
        $serializeMemBefore = memory_get_usage(true);
        $serializeStart = hrtime(true);
        $serialized = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $serialized[] = unserialize(serialize($this->spreadsheet));
        }
        $serializeEnd = hrtime(true);
        $serializeMemAfter = memory_get_usage(true);
        foreach ($serialized as $s) {
            /** @var Spreadsheet $s */
            $s->disconnectWorksheets();
        }
        unset($serialized);
        gc_collect_cycles();

        $serializeTimeMs = ($serializeEnd - $serializeStart) / 1_000_000;
        $serializeAvgMs = $serializeTimeMs / $iterations;
        $serializeMemDeltaMb = ($serializeMemAfter - $serializeMemBefore) / 1024 / 1024;

        // Output results for manual comparison
        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Spreadsheet Copy Benchmark ({$totalCells} cells, {$iterations} iterations) ===\n");
        fwrite(STDERR, sprintf("  PHP version:           %s (%s)\n", PHP_VERSION, PHP_OS));
        fwrite(STDERR, sprintf("  clone:                 %.2f ms avg (%.2f ms total)\n", $cloneAvgMs, $cloneTimeMs));
        fwrite(STDERR, sprintf("  serialize/unserialize: %.2f ms avg (%.2f ms total)\n", $serializeAvgMs, $serializeTimeMs));
        fwrite(STDERR, sprintf("  Clone memory delta:    %.2f MB\n", $cloneMemDeltaMb));
        fwrite(STDERR, sprintf("  Serialize memory delta: %.2f MB\n", $serializeMemDeltaMb));
        fwrite(STDERR, "\n");

        // Assert both approaches produce spreadsheets with the expected cell count
        $cloneCopy = clone $this->spreadsheet;
        $cloneCells = 0;
        foreach ($cloneCopy->getWorksheetIterator() as $sheet) {
            $cloneCells += count($sheet->getCellCollection()->getCoordinates());
        }
        self::assertSame($totalCells, $cloneCells, 'Clone must preserve all cells');
        $cloneCopy->disconnectWorksheets();

        /** @var Spreadsheet $serializeCopy */
        $serializeCopy = unserialize(serialize($this->spreadsheet));
        $serializeCells = 0;
        foreach ($serializeCopy->getWorksheetIterator() as $sheet) {
            $serializeCells += count($sheet->getCellCollection()->getCoordinates());
        }
        self::assertSame($totalCells, $serializeCells, 'Serialize copy must preserve all cells');
        $serializeCopy->disconnectWorksheets();
    }
}
