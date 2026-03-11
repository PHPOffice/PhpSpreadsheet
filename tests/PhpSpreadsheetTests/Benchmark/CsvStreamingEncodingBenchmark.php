<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Benchmark;

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\Group('benchmark')]
/**
 * @codeCoverageIgnore
 */
class CsvStreamingEncodingBenchmark extends TestCase
{
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
     * Generate a CSV file in ISO-8859-1 encoding with accented characters.
     */
    private function generateIso88591Csv(int $rows, int $cols = 10): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_bench_iso_') . '.csv';
        $this->tempFiles[] = $tempFile;

        $handle = fopen($tempFile, 'wb');
        self::assertNotFalse($handle);

        // Accented characters in ISO-8859-1: é(0xE9), ö(0xF6), ü(0xFC), ñ(0xF1), à(0xE0), ç(0xE7)
        $specialChars = ["\xE9", "\xF6", "\xFC", "\xF1", "\xE0", "\xE7"];

        for ($r = 0; $r < $rows; ++$r) {
            $fields = [];
            for ($c = 0; $c < $cols; ++$c) {
                $accent = $specialChars[($r + $c) % count($specialChars)];
                $fields[] = sprintf('Cell_%d_%d_%s_data_with_special_chars_%s', $r, $c, $accent, $accent);
            }
            fwrite($handle, implode(',', $fields) . "\n");
        }

        fclose($handle);

        return $tempFile;
    }

    /**
     * Generate a CSV file in UTF-16LE encoding with BOM and accented characters.
     */
    private function generateUtf16LeCsv(int $rows, int $cols = 10): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_bench_u16_') . '.csv';
        $this->tempFiles[] = $tempFile;

        $handle = fopen($tempFile, 'wb');
        self::assertNotFalse($handle);

        // Write UTF-16LE BOM
        fwrite($handle, "\xFF\xFE");

        $specialChars = ['é', 'ö', 'ü', 'ñ', 'à', 'ç'];

        for ($r = 0; $r < $rows; ++$r) {
            $fields = [];
            for ($c = 0; $c < $cols; ++$c) {
                $accent = $specialChars[($r + $c) % count($specialChars)];
                $fields[] = sprintf('Cell_%d_%d_%s_data_with_special_chars_%s', $r, $c, $accent, $accent);
            }
            $line = implode(',', $fields) . "\n";
            // Convert UTF-8 line to UTF-16LE
            $converted = mb_convert_encoding($line, 'UTF-16LE', 'UTF-8');
            fwrite($handle, $converted);
        }

        fclose($handle);

        return $tempFile;
    }

    /**
     * Read a CSV file with the given encoding and return timing/memory stats.
     *
     * @return array{time_ms: float, memory_used_mb: float, rows: int}
     */
    private function benchmarkRead(string $filename, string $encoding): array
    {
        // Force garbage collection before measurement
        gc_collect_cycles();
        $memoryBefore = memory_get_usage(true);

        $reader = new CsvReader();
        $reader->setInputEncoding($encoding);

        $startTime = hrtime(true);
        $spreadsheet = $reader->load($filename);
        $endTime = hrtime(true);

        $memoryAfter = memory_get_usage(true);

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->getHighestRow();

        $spreadsheet->disconnectWorksheets();

        return [
            'time_ms' => ($endTime - $startTime) / 1_000_000,
            'memory_used_mb' => ($memoryAfter - $memoryBefore) / 1024 / 1024,
            'rows' => $rows,
        ];
    }

    public function testStreamingEncodingIso88591(): void
    {
        $filename = $this->generateIso88591Csv(5000);
        $fileSize = filesize($filename);

        $result = $this->benchmarkRead($filename, 'ISO-8859-1');

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== ISO-8859-1 Streaming Encoding Benchmark ===\n");
        fwrite(STDERR, sprintf("File size: %.2f MB\n", $fileSize / 1024 / 1024));
        fwrite(STDERR, sprintf("Rows read: %d\n", $result['rows']));
        fwrite(STDERR, sprintf("Time: %.2f ms\n", $result['time_ms']));
        fwrite(STDERR, sprintf("Memory used: %.2f MB\n", $result['memory_used_mb']));

        self::assertSame(5000, $result['rows']);
    }

    public function testStreamingEncodingUtf16Le(): void
    {
        $filename = $this->generateUtf16LeCsv(5000);
        $fileSize = filesize($filename);

        $result = $this->benchmarkRead($filename, 'UTF-16LE');

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== UTF-16LE Streaming Encoding Benchmark ===\n");
        fwrite(STDERR, sprintf("File size: %.2f MB\n", $fileSize / 1024 / 1024));
        fwrite(STDERR, sprintf("Rows read: %d\n", $result['rows']));
        fwrite(STDERR, sprintf("Time: %.2f ms\n", $result['time_ms']));
        fwrite(STDERR, sprintf("Memory used: %.2f MB\n", $result['memory_used_mb']));

        self::assertSame(5000, $result['rows']);
    }

    public function testMemoryScalingWithFileSize(): void
    {
        // Generate small and large ISO-8859-1 CSV files
        $smallFile = $this->generateIso88591Csv(1000);
        $largeFile = $this->generateIso88591Csv(5000);

        $smallFileSize = filesize($smallFile);
        $largeFileSize = filesize($largeFile);

        $smallResult = $this->benchmarkRead($smallFile, 'ISO-8859-1');
        $largeResult = $this->benchmarkRead($largeFile, 'ISO-8859-1');

        $fileSizeRatio = $largeFileSize / $smallFileSize;
        $memoryRatio = $largeResult['memory_used_mb'] / $smallResult['memory_used_mb'];

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Memory Scaling Comparison (ISO-8859-1) ===\n");
        fwrite(STDERR, sprintf("Small file (1000 rows): %.2f MB file, %.2f MB memory used, %.2f ms\n", $smallFileSize / 1024 / 1024, $smallResult['memory_used_mb'], $smallResult['time_ms']));
        fwrite(STDERR, sprintf("Large file (5000 rows): %.2f MB file, %.2f MB memory used, %.2f ms\n", $largeFileSize / 1024 / 1024, $largeResult['memory_used_mb'], $largeResult['time_ms']));
        fwrite(STDERR, sprintf("File size ratio (large/small): %.2fx\n", $fileSizeRatio));
        fwrite(STDERR, sprintf("Memory ratio (large/small): %.2fx\n", $memoryRatio));
        fwrite(STDERR, sprintf("Streaming keeps memory sub-linear: %s\n", $memoryRatio < $fileSizeRatio ? 'YES' : 'NO'));

        // With streaming, memory should not scale linearly with file size.
        // The encoding conversion itself uses constant memory (CHUNK_SIZE),
        // though the spreadsheet object will still grow with row count.
        // We verify memory ratio is less than file size ratio.
        self::assertLessThan(
            $fileSizeRatio,
            $memoryRatio,
            'Peak memory should grow sub-linearly relative to file size due to streaming encoding conversion'
        );

        // Also generate small and large UTF-16LE files
        $smallUtf16 = $this->generateUtf16LeCsv(1000);
        $largeUtf16 = $this->generateUtf16LeCsv(5000);

        $smallUtf16Size = filesize($smallUtf16);
        $largeUtf16Size = filesize($largeUtf16);

        $smallUtf16Result = $this->benchmarkRead($smallUtf16, 'UTF-16LE');
        $largeUtf16Result = $this->benchmarkRead($largeUtf16, 'UTF-16LE');

        $utf16FileSizeRatio = $largeUtf16Size / $smallUtf16Size;
        $utf16MemoryRatio = $largeUtf16Result['memory_used_mb'] / $smallUtf16Result['memory_used_mb'];

        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Memory Scaling Comparison (UTF-16LE) ===\n");
        fwrite(STDERR, sprintf("Small file (1000 rows): %.2f MB file, %.2f MB memory used, %.2f ms\n", $smallUtf16Size / 1024 / 1024, $smallUtf16Result['memory_used_mb'], $smallUtf16Result['time_ms']));
        fwrite(STDERR, sprintf("Large file (5000 rows): %.2f MB file, %.2f MB memory used, %.2f ms\n", $largeUtf16Size / 1024 / 1024, $largeUtf16Result['memory_used_mb'], $largeUtf16Result['time_ms']));
        fwrite(STDERR, sprintf("File size ratio (large/small): %.2fx\n", $utf16FileSizeRatio));
        fwrite(STDERR, sprintf("Memory ratio (large/small): %.2fx\n", $utf16MemoryRatio));
        fwrite(STDERR, sprintf("Streaming keeps memory sub-linear: %s\n", $utf16MemoryRatio < $utf16FileSizeRatio ? 'YES' : 'NO'));

        self::assertLessThan(
            $utf16FileSizeRatio,
            $utf16MemoryRatio,
            'Peak memory should grow sub-linearly relative to file size due to streaming encoding conversion (UTF-16LE)'
        );
    }
}
