<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class StreamingMemoryTest extends TestCase
{
    public function testMemoryIsIndependentOfRowCount(): void
    {
        if (!function_exists('proc_open')) {
            self::markTestSkipped('Separate memory measurements require proc_open.');
        }
        $peaks = [];
        foreach ([100000, 200000] as $rows) {
            $result = $this->runBenchmark($rows);
            self::assertSame(0, $result['exitCode'], $result['stderr']);
            $measurement = json_decode($result['stdout'], true, 512, JSON_THROW_ON_ERROR);
            self::assertIsArray($measurement);
            self::assertSame($rows, $measurement['rows']);
            self::assertGreaterThan(0, $measurement['file_size_bytes']);
            self::assertIsInt($measurement['peak_memory_bytes']);
            $peaks[] = $measurement['peak_memory_bytes'];
        }
        // Each measurement starts in a fresh process, including autoloading and
        // archive assembly. ZipStream's fixed read block is saturated at both sizes.
        self::assertLessThan($peaks[0] + 4 * 1024 * 1024, $peaks[1]);
        self::assertLessThan(64 * 1024 * 1024, $peaks[1]);
    }

    /** @return array{exitCode: int, stdout: string, stderr: string} */
    private function runBenchmark(int $rows): array
    {
        $process = proc_open(
            [PHP_BINARY, '-d', 'memory_limit=128M', __DIR__ . '/../../../../Benchmark/bench.php', 'streaming', (string) $rows],
            [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']],
            $pipes
        );
        if ($process === false) {
            throw new RuntimeException('Could not start streaming memory benchmark.');
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return ['exitCode' => proc_close($process), 'stdout' => $stdout ?: '', 'stderr' => $stderr ?: ''];
    }
}
