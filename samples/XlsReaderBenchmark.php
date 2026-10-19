<?php

/**
 * Benchmark: XLS (BIFF8) Reader Performance.
 *
 * Generates .xls files of varying sizes with mixed numeric/string/formula
 * content, then times repeated Xls reader loads, reporting median load
 * times and peak memory usage.
 *
 * Usage:
 *   php samples/XlsReaderBenchmark.php [iterations] [cellCounts]
 *
 * Examples:
 *   php samples/XlsReaderBenchmark.php                # 5 iterations, 10000/50000/100000 cells
 *   php samples/XlsReaderBenchmark.php 3 20000,80000  # 3 iterations, 20000 and 80000 cells
 */

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;

require __DIR__ . '/../vendor/autoload.php';

$iterations = max(1, (int) ($argv[1] ?? 5));
$cellCounts = array_map(intval(...), explode(',', $argv[2] ?? '10000,50000,100000'));
$columnCount = 10;

echo "=== XLS (BIFF8) Reader Benchmark ===\n";
echo 'PHP version: ' . PHP_VERSION . "\n";
echo "Iterations per size: {$iterations}\n";
echo 'Cell counts: ' . implode(', ', $cellCounts) . "\n\n";

/**
 * Build an .xls file with mixed numeric, string and formula cells,
 * plus enough styling to exercise the XF index mapping paths.
 */
function createXlsFile(int $cellCount, int $columnCount): string
{
    $rowCount = max(1, intdiv($cellCount, $columnCount));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Benchmark');

    // Header row with styling
    for ($col = 1; $col <= $columnCount; ++$col) {
        $sheet->setCellValue([$col, 1], "Column{$col}");
        $sheet->getStyle([$col, 1])->getFont()->setBold(true);
    }

    // Repeating strings exercise the shared string table (SST)
    $labels = ['Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo', 'Foxtrot'];
    $labelCount = count($labels);

    for ($row = 2; $row <= $rowCount + 1; ++$row) {
        // Columns 1-4: numeric (RK / NUMBER records)
        $sheet->setCellValue([1, $row], $row);
        $sheet->setCellValue([2, $row], $row * 3.14159);
        $sheet->setCellValue([3, $row], $row % 100);
        $sheet->setCellValue([4, $row], round($row * 1.37, 2));

        // Columns 5-8: strings (LABELSST records)
        $sheet->setCellValue([5, $row], $labels[$row % $labelCount]);
        $sheet->setCellValue([6, $row], "Item {$row}");
        $sheet->setCellValue([7, $row], $labels[($row + 3) % $labelCount] . ' ' . ($row % 50));
        $sheet->setCellValue([8, $row], 'Ref-' . str_pad((string) $row, 8, '0', STR_PAD_LEFT));

        // Columns 9-10: formulas (FORMULA records)
        $sheet->setCellValue([9, $row], "=B{$row}*D{$row}");
        $sheet->setCellValue([10, $row], "=A{$row}+C{$row}");
    }

    // Number formats so cells carry non-default XF indexes
    $sheet->getStyle("B2:B{$rowCount}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("D2:D{$rowCount}")->getNumberFormat()->setFormatCode('0.00');

    $tempFile = tempnam(sys_get_temp_dir(), 'xls_bench_');
    if ($tempFile === false) {
        throw new RuntimeException('Unable to create temporary file');
    }

    $writer = new XlsWriter($spreadsheet);
    $writer->save($tempFile);
    $spreadsheet->disconnectWorksheets();

    return $tempFile;
}

/** @param float[] $values */
function median(array $values): float
{
    sort($values);
    $count = count($values);
    $middle = intdiv($count, 2);

    if ($count % 2 === 0) {
        return ($values[$middle - 1] + $values[$middle]) / 2;
    }

    return $values[$middle];
}

$results = [];

foreach ($cellCounts as $cellCount) {
    echo "--- {$cellCount} cells ---\n";

    echo "Generating test file...\n";
    $tempFile = createXlsFile($cellCount, $columnCount);
    $fileSize = filesize($tempFile);
    echo sprintf("File size: %.1fKB\n", ($fileSize ?: 0) / 1024);

    $reader = new XlsReader();

    // Warm up (autoloading, opcache)
    $warmup = $reader->load($tempFile);
    $warmup->disconnectWorksheets();
    unset($warmup);
    gc_collect_cycles();

    $times = [];
    for ($i = 0; $i < $iterations; ++$i) {
        $start = hrtime(true);
        $spreadsheet = $reader->load($tempFile);
        $elapsedMs = (hrtime(true) - $start) / 1e6;

        $times[] = $elapsedMs;
        echo sprintf("Iteration %d: %.1fms\n", $i + 1, $elapsedMs);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();
    }

    $medianMs = median($times);
    $peakMb = memory_get_peak_usage(true) / 1048576;
    echo sprintf("Median: %.1fms, Peak memory: %.1fMB\n\n", $medianMs, $peakMb);

    $results[] = [
        'cells' => $cellCount,
        'median' => $medianMs,
        'peak' => $peakMb,
    ];

    unlink($tempFile);
}

echo "--- Results ---\n";
echo sprintf("%10s | %12s | %12s\n", 'Cells', 'Median (ms)', 'Peak (MB)');
foreach ($results as $result) {
    echo sprintf("%10d | %12.1f | %12.1f\n", $result['cells'], $result['median'], $result['peak']);
}
