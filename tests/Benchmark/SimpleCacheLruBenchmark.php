<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache3;
use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\Group('benchmark')]
class SimpleCacheLruBenchmark extends TestCase
{
    private const ENTRY_COUNT = 50_000;

    private const BOUNDED_MAX_SIZE = 10_000;

    private const THROUGHPUT_OPS = 100_000;

    /**
     * Build a dummy cell value roughly similar to what PhpSpreadsheet stores.
     *
     * @return array<string, mixed>
     */
    private static function dummyValue(int $i): array
    {
        return [
            'value' => "Cell value {$i}",
            'style' => str_repeat('x', 64),
            'formula' => null,
            'dataType' => 's',
        ];
    }

    public function testUnboundedCacheMemory(): void
    {
        $cache = new SimpleCache3(0);

        gc_collect_cycles();
        $memBefore = memory_get_usage();

        for ($i = 0; $i < self::ENTRY_COUNT; ++$i) {
            $cache->set("cell_{$i}", self::dummyValue($i));
        }

        $memAfter = memory_get_usage();
        $memUsedBytes = $memAfter - $memBefore;
        $memUsedMb = round($memUsedBytes / 1024 / 1024, 2);

        fwrite(STDERR, sprintf(
            "\n[Unbounded cache] Inserted %s entries | Memory: %.2f MB\n",
            number_format(self::ENTRY_COUNT),
            $memUsedMb
        ));

        // Verify all entries are present
        self::assertTrue($cache->has('cell_0'));
        self::assertTrue($cache->has('cell_' . (self::ENTRY_COUNT - 1)));
    }

    public function testBoundedCacheMemory(): void
    {
        $cache = new SimpleCache3(self::BOUNDED_MAX_SIZE);

        gc_collect_cycles();
        $memBefore = memory_get_usage();

        for ($i = 0; $i < self::ENTRY_COUNT; ++$i) {
            $cache->set("cell_{$i}", self::dummyValue($i));
        }

        $memAfter = memory_get_usage();
        $memUsedBytes = $memAfter - $memBefore;
        $memUsedMb = round($memUsedBytes / 1024 / 1024, 2);

        fwrite(STDERR, sprintf(
            "\n[Bounded cache maxSize=%s] Inserted %s entries | Memory: %.2f MB\n",
            number_format(self::BOUNDED_MAX_SIZE),
            number_format(self::ENTRY_COUNT),
            $memUsedMb
        ));

        // Old entries should have been evicted
        self::assertFalse($cache->has('cell_0'), 'Oldest entry should be evicted');

        // Most recent entries within the window should exist
        $lastKey = 'cell_' . (self::ENTRY_COUNT - 1);
        self::assertTrue($cache->has($lastKey), 'Most recent entry should be present');
    }

    public function testMemorySavingsComparison(): void
    {
        // Unbounded
        $unbounded = new SimpleCache3(0);
        gc_collect_cycles();
        $memBeforeUnbounded = memory_get_usage();

        for ($i = 0; $i < self::ENTRY_COUNT; ++$i) {
            $unbounded->set("cell_{$i}", self::dummyValue($i));
        }

        $memUnbounded = memory_get_usage() - $memBeforeUnbounded;

        // Free unbounded cache
        $unbounded->clear();
        unset($unbounded);
        gc_collect_cycles();

        // Bounded
        $bounded = new SimpleCache3(self::BOUNDED_MAX_SIZE);
        gc_collect_cycles();
        $memBeforeBounded = memory_get_usage();

        for ($i = 0; $i < self::ENTRY_COUNT; ++$i) {
            $bounded->set("cell_{$i}", self::dummyValue($i));
        }

        $memBounded = memory_get_usage() - $memBeforeBounded;

        $savingsPercent = $memUnbounded > 0
            ? round((1 - $memBounded / $memUnbounded) * 100, 1)
            : 0;

        fwrite(STDERR, sprintf(
            "\n[Memory comparison] Unbounded: %.2f MB | Bounded (maxSize=%s): %.2f MB | Savings: %.1f%%\n",
            $memUnbounded / 1024 / 1024,
            number_format(self::BOUNDED_MAX_SIZE),
            $memBounded / 1024 / 1024,
            $savingsPercent
        ));

        self::assertGreaterThan(0, $savingsPercent, 'Bounded cache should use less memory than unbounded');
    }

    public function testSetGetThroughput(): void
    {
        // Unbounded throughput
        $unbounded = new SimpleCache3(0);
        $startUnbounded = hrtime(true);

        for ($i = 0; $i < self::THROUGHPUT_OPS; ++$i) {
            $unbounded->set("key_{$i}", self::dummyValue($i));
        }
        for ($i = 0; $i < self::THROUGHPUT_OPS; ++$i) {
            $unbounded->get("key_{$i}");
        }

        $elapsedUnbounded = (hrtime(true) - $startUnbounded) / 1e6; // ms

        $unbounded->clear();
        unset($unbounded);

        // Bounded throughput
        $bounded = new SimpleCache3(self::BOUNDED_MAX_SIZE);
        $startBounded = hrtime(true);

        for ($i = 0; $i < self::THROUGHPUT_OPS; ++$i) {
            $bounded->set("key_{$i}", self::dummyValue($i));
        }
        for ($i = 0; $i < self::THROUGHPUT_OPS; ++$i) {
            $bounded->get("key_{$i}");
        }

        $elapsedBounded = (hrtime(true) - $startBounded) / 1e6; // ms

        $opsPerSecUnbounded = self::THROUGHPUT_OPS * 2 / ($elapsedUnbounded / 1000);
        $opsPerSecBounded = self::THROUGHPUT_OPS * 2 / ($elapsedBounded / 1000);

        fwrite(STDERR, sprintf(
            "\n[Throughput] Unbounded: %.0f ms (%.0f ops/s) | Bounded (maxSize=%s): %.0f ms (%.0f ops/s)\n",
            $elapsedUnbounded,
            $opsPerSecUnbounded,
            number_format(self::BOUNDED_MAX_SIZE),
            $elapsedBounded,
            $opsPerSecBounded
        ));

        // Both should complete — we're just measuring, not asserting a hard threshold
        self::assertGreaterThan(0, $elapsedUnbounded);
        self::assertGreaterThan(0, $elapsedBounded);
    }

    public function testEvictionOverhead(): void
    {
        // Measure time for get operations that promote entries (LRU tracking cost).
        $cache = new SimpleCache3(self::BOUNDED_MAX_SIZE);

        // Pre-fill the cache
        for ($i = 0; $i < self::BOUNDED_MAX_SIZE; ++$i) {
            $cache->set("key_{$i}", self::dummyValue($i));
        }

        // Time a large number of get operations that promote entries
        $getOps = self::THROUGHPUT_OPS;
        $start = hrtime(true);

        for ($i = 0; $i < $getOps; ++$i) {
            // Access keys in a pattern that constantly promotes entries
            $key = 'key_' . ($i % self::BOUNDED_MAX_SIZE);
            $cache->get($key);
        }

        $elapsedMs = (hrtime(true) - $start) / 1e6;
        $opsPerSec = $getOps / ($elapsedMs / 1000);
        $avgMicroseconds = ($elapsedMs * 1000) / $getOps;

        fwrite(STDERR, sprintf(
            "\n[Eviction overhead] %s get-with-promote ops in %.0f ms | %.0f ops/s | %.2f us/op\n",
            number_format($getOps),
            $elapsedMs,
            $opsPerSec,
            $avgMicroseconds
        ));

        // The average cost per promote should be well under 100 microseconds
        self::assertLessThan(
            100.0,
            $avgMicroseconds,
            'LRU promotion overhead should be under 100 microseconds per operation'
        );
    }
}
