<?php

/**
 * Benchmark: Sequential vs Parallel Xlsx Writing.
 *
 * Usage:
 *   php samples/ParallelBenchmark.php [sheets] [rows]
 *
 * Examples:
 *   php samples/ParallelBenchmark.php          # 5 sheets, 5000 rows each
 *   php samples/ParallelBenchmark.php 10 10000 # 10 sheets, 10000 rows each
 */

use PhpOffice\PhpSpreadsheet\Parallel\Backend\PcntlBackend;
use PhpOffice\PhpSpreadsheet\Parallel\CpuDetector;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . '/../vendor/autoload.php';

$sheetCount = (int) ($argv[1] ?? 5);
$rowCount = (int) ($argv[2] ?? 5000);
$colCount = 10;

echo "=== Parallel Xlsx Writing Benchmark ===\n";
echo "Sheets: {$sheetCount}, Rows/sheet: {$rowCount}, Cols/sheet: {$colCount}\n";
echo 'CPU cores detected: ' . CpuDetector::detectCpuCount() . "\n";
echo 'pcntl available: ' . (PcntlBackend::isAvailable() ? 'yes' : 'no') . "\n";
echo 'Total cells: ' . ($sheetCount * $rowCount * $colCount) . "\n\n";

// Build spreadsheet
echo "Building spreadsheet...\n";
$buildStart = microtime(true);
$spreadsheet = new Spreadsheet();

for ($s = 0; $s < $sheetCount; ++$s) {
    $sheet = ($s === 0) ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle("Sheet{$s}");

    for ($row = 1; $row <= $rowCount; ++$row) {
        for ($col = 1; $col <= $colCount; ++$col) {
            $sheet->setCellValue([$col, $row], "S{$s}R{$row}C{$col}");
        }
    }
}
$buildTime = microtime(true) - $buildStart;
$peakAfterBuild = memory_get_peak_usage(true);
echo sprintf("Build time: %.3fs, Peak memory: %.1fMB\n\n", $buildTime, $peakAfterBuild / 1048576);

// Sequential write
echo "--- Sequential write ---\n";
$tempSeq = tempnam(sys_get_temp_dir(), 'bench_seq_');
$seqStart = microtime(true);
$writer = new Xlsx($spreadsheet);
$writer->save($tempSeq);
$seqTime = microtime(true) - $seqStart;
$seqSize = filesize($tempSeq);
echo sprintf("Time: %.3fs, File size: %.1fKB\n", $seqTime, ($seqSize ?: 0) / 1024);
@unlink($tempSeq);

// Parallel write
if (PcntlBackend::isAvailable()) {
    echo "\n--- Parallel write ---\n";

    $tempPar = tempnam(sys_get_temp_dir(), 'bench_par_');
    $parStart = microtime(true);
    $writer2 = new Xlsx($spreadsheet);
    $writer2->setParallelEnabled(true);
    // $writer2->setMaxWorkers(4); // Optional: override auto-detect
    $writer2->save($tempPar);
    $parTime = microtime(true) - $parStart;
    $parSize = filesize($tempPar);
    echo sprintf("Time: %.3fs, File size: %.1fKB\n", $parTime, ($parSize ?: 0) / 1024);
    @unlink($tempPar);

    echo "\n--- Results ---\n";
    $speedup = $seqTime / $parTime;
    $saved = (1 - ($parTime / $seqTime)) * 100;
    echo sprintf("Sequential: %.3fs\n", $seqTime);
    echo sprintf("Parallel:   %.3fs\n", $parTime);
    echo sprintf("Speedup:    %.2fx (%.1f%% faster)\n", $speedup, $saved);
} else {
    echo "\npcntl not available — parallel benchmark skipped.\n";
}

echo sprintf("\nPeak memory: %.1fMB\n", memory_get_peak_usage(true) / 1048576);

$spreadsheet->disconnectWorksheets();
