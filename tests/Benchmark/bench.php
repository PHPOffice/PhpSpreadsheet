<?php

declare(strict_types=1);

/*
 * Benchmark: standard Xlsx writer vs streaming Xlsx writer.
 *
 * Writes $rows rows of 8 mixed-type cells (string, int, float, bool,
 * DateTimeImmutable, string, float, bool) with one engine, in its own
 * process, and prints one JSON line with wall time, peak memory, and
 * output file size.
 *
 * Usage: php tests/Benchmark/bench.php <standard|streaming> [rows]
 *
 * Run each engine in its own process (this script does not fork) so wall
 * time and memory are never shared between runs. See README.md in this
 * directory for the full recipe and recorded results.
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;

require __DIR__ . '/../../vendor/autoload.php';

/** @return array<int, mixed> */
function benchBuildRow(int $row): array
{
    return [
        'Name ' . $row,
        $row,
        $row * 1.5,
        $row % 2 === 0,
        new DateTimeImmutable('2026-01-01 00:00:00'),
        'Description for row ' . $row,
        $row * 3.25,
        $row % 3 === 0,
    ];
}

function benchRunStandard(int $rows, string $file): void
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    for ($row = 1; $row <= $rows; ++$row) {
        $sheet->fromArray(benchBuildRow($row), null, 'A' . $row);
    }
    $writer = new Xlsx($spreadsheet);
    $writer->save($file);
    $spreadsheet->disconnectWorksheets();
}

function benchRunStreaming(int $rows, string $file): void
{
    $writer = new StreamingWriter($file);
    $sheet = $writer->startSheet('Data');
    for ($row = 1; $row <= $rows; ++$row) {
        $sheet->appendRow(benchBuildRow($row));
    }
    $writer->close();
}

/** @param array<int, string> $argv */
function benchMain(array $argv): int
{
    $engine = $argv[1] ?? null;
    if ($engine !== 'standard' && $engine !== 'streaming') {
        fwrite(STDERR, "Usage: php bench.php <standard|streaming> [rows]\n");

        return 1;
    }
    $rows = isset($argv[2]) ? (int) $argv[2] : 200000;

    $file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_bench_');
    if ($file === false) {
        fwrite(STDERR, "Could not create a temporary file.\n");

        return 1;
    }

    $start = hrtime(true);
    if ($engine === 'standard') {
        benchRunStandard($rows, $file);
    } else {
        benchRunStreaming($rows, $file);
    }
    $elapsedNs = hrtime(true) - $start;

    $peakMemoryBytes = memory_get_peak_usage(true);
    $fileSizeBytes = filesize($file);
    unlink($file);

    $result = [
        'engine' => $engine,
        'rows' => $rows,
        'elapsed_ms' => $elapsedNs / 1_000_000,
        'peak_memory_bytes' => $peakMemoryBytes,
        'file_size_bytes' => $fileSizeBytes,
    ];

    $encoded = json_encode($result);
    echo ($encoded === false ? '{}' : $encoded) . "\n";

    return 0;
}

/** @var array<int, string> $arguments */
$arguments = $_SERVER['argv'] ?? [];
exit(benchMain($arguments));
