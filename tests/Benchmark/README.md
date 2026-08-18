# Streaming writer benchmark

`bench.php` compares the standard `Writer\Xlsx` engine against
`Writer\Xlsx\Streaming\StreamingWriter`. It writes rows of 8 mixed-type
cells (string, int, float, bool, `DateTimeImmutable`, string, float, bool)
and prints one JSON line with wall time, peak memory, and output file
size.

These scripts are not PHPUnit tests. `phpunit.xml.dist` only scans
`tests/PhpSpreadsheetTests`, so `tests/Benchmark` is never picked up by
`phpunit` runs.

## Usage

```
php tests/Benchmark/bench.php <standard|streaming> [rows]
```

`rows` defaults to 200000.

Run each engine in its own process, so peak memory and wall time are never
shared between engines or between runs of the same engine. For a stable
median, run each engine 3 times:

```
php -d memory_limit=4G tests/Benchmark/bench.php standard 200000
php -d memory_limit=4G tests/Benchmark/bench.php standard 200000
php -d memory_limit=4G tests/Benchmark/bench.php standard 200000
php -d memory_limit=4G tests/Benchmark/bench.php streaming 200000
php -d memory_limit=4G tests/Benchmark/bench.php streaming 200000
php -d memory_limit=4G tests/Benchmark/bench.php streaming 200000
```

Take the median `elapsed_ms` and median `peak_memory_bytes` of each set of
3 runs.

## Recorded results

Environment: PHP 8.5.2 (cli, NTS, Opcache), Darwin 24.5.0 arm64
(macOS, Apple Silicon), 2026-08-18. 200,000 rows x 8 columns, 3 runs per
engine in separate `php -d memory_limit=4G` processes, medians reported.

| Engine | Median wall time | Median peak memory | Output file size |
| --- | --- | --- | --- |
| Standard (`Spreadsheet` + `Writer\Xlsx`) | 123.35 s | 1113.36 MB | 10,547,334 bytes |
| Streaming (`StreamingWriter`) | 6.73 s | 46.42 MB | 9,398,884 bytes |
| **Ratio (standard / streaming)** | **~18.3x slower** | **~24.0x more memory** | ~1.12x larger |

The streaming writer is about 18 times faster and uses about 24 times less
peak memory for this workload, while producing a slightly smaller file
(inline strings avoid `sharedStrings.xml` overhead for this
mostly-unique-string dataset). Peak memory and file size are stable across
runs; wall time varies by less than 4%.
