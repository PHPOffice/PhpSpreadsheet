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

use Composer\InstalledVersions;
use PhpOffice\PhpSpreadsheetBenchmarks\StreamingWriterBenchmark;

require __DIR__ . '/../../vendor/autoload.php';

/** @param array<int, string> $argv */
function benchMain(array $argv): int
{
    $engine = $argv[1] ?? null;
    if ($engine !== 'standard' && $engine !== 'streaming') {
        fwrite(STDERR, "Usage: php bench.php <standard|streaming> [rows]\n");

        return 1;
    }
    $rows = isset($argv[2]) ? (int) $argv[2] : 200000;
    if ($rows < 1) {
        fwrite(STDERR, "Rows must be positive.\n");

        return 1;
    }

    $file = tempnam(sys_get_temp_dir(), 'phpspreadsheet_bench_');
    if ($file === false) {
        fwrite(STDERR, "Could not create a temporary file.\n");

        return 1;
    }

    try {
        $start = hrtime(true);
        if ($engine === 'standard') {
            StreamingWriterBenchmark::writeStandard($rows, $file);
        } else {
            StreamingWriterBenchmark::writeStreaming($rows, $file);
        }
        $elapsedNs = hrtime(true) - $start;
        $peakMemoryBytes = memory_get_peak_usage(true);
        $fileSizeBytes = filesize($file);
    } finally {
        unlink($file);
    }

    $result = [
        'engine' => $engine,
        'php_version' => PHP_VERSION,
        'zipstream_version' => InstalledVersions::getPrettyVersion('maennchen/zipstream-php'),
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
