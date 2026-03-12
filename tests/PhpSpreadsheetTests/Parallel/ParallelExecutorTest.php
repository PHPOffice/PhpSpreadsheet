<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Parallel\Backend\PcntlBackend;
use PhpOffice\PhpSpreadsheet\Parallel\Backend\SequentialBackend;
use PhpOffice\PhpSpreadsheet\Parallel\ParallelExecutor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ParallelExecutorTest extends TestCase
{
    public function testEmptyTasks(): void
    {
        $executor = new ParallelExecutor();
        $results = $executor->map([], fn (int $x): int => $x * 2);
        self::assertSame([], $results);
    }

    public function testSingleTask(): void
    {
        $executor = new ParallelExecutor();
        $results = $executor->map([5], fn (int $x): int => $x * 2);
        self::assertSame([10], $results);
    }

    public function testSequentialFallbackWithNoMaxWorkers(): void
    {
        // No maxWorkers set, auto-detect may give >= 2 workers but sequential backend forces sequential
        $executor = new ParallelExecutor(new SequentialBackend());
        $results = $executor->map([1, 2, 3], fn (int $x): int => $x * 2);
        self::assertSame([2, 4, 6], $results);
    }

    public function testSequentialBackendDirectly(): void
    {
        $backend = new SequentialBackend();
        $results = $backend->execute([1, 2, 3], fn (int $x): int => $x + 10, 4);
        self::assertSame([11, 12, 13], $results);
    }

    public function testSequentialBackendIsAlwaysAvailable(): void
    {
        self::assertTrue(SequentialBackend::isAvailable());
    }

    public function testResultOrderPreserved(): void
    {
        $executor = new ParallelExecutor(new SequentialBackend());
        $results = $executor->map(
            [3, 1, 4, 1, 5, 9],
            fn (int $x): string => "val_{$x}"
        );
        self::assertSame(['val_3', 'val_1', 'val_4', 'val_1', 'val_5', 'val_9'], $results);
    }

    public function testPcntlBackendAvailability(): void
    {
        $available = PcntlBackend::isAvailable();
        if (function_exists('pcntl_fork') && PHP_OS_FAMILY !== 'Windows') {
            self::assertTrue($available);
        } else {
            self::assertFalse($available);
        }
    }

    public function testPcntlBackendParallelExecution(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 2);
        $results = $executor->map(
            [10, 20, 30, 40],
            fn (int $x): int => $x * 3
        );
        self::assertSame([30, 60, 90, 120], $results);
    }

    public function testPcntlBackendWithStringResults(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 3);
        $results = $executor->map(
            ['hello', 'world', 'foo'],
            fn (string $s): string => strtoupper($s)
        );
        self::assertSame(['HELLO', 'WORLD', 'FOO'], $results);
    }

    public function testPcntlBackendWithArrayResults(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 2);
        $results = $executor->map(
            [1, 2],
            fn (int $x): array => ['value' => $x, 'doubled' => $x * 2]
        );
        self::assertSame(
            [
                ['value' => 1, 'doubled' => 2],
                ['value' => 2, 'doubled' => 4],
            ],
            $results
        );
    }

    public function testPcntlBackendWithMoreTasksThanWorkers(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 2);
        // 6 tasks with 2 workers = 3 batches
        $results = $executor->map(
            [1, 2, 3, 4, 5, 6],
            fn (int $x): int => $x * $x
        );
        self::assertSame([1, 4, 9, 16, 25, 36], $results);
    }

    public function testPcntlBackendChildErrorPropagation(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Parallel task .* failed/');

        $executor = new ParallelExecutor(new PcntlBackend(), 2);
        $executor->map(
            [1, 2],
            function (int $x): int {
                if ($x === 2) {
                    throw new RuntimeException('Task failed intentionally');
                }

                return $x;
            }
        );
    }

    public function testConstructorWithExplicitBackend(): void
    {
        $backend = new SequentialBackend();
        $executor = new ParallelExecutor($backend, 4);

        $results = $executor->map([1, 2, 3], fn (int $x): int => $x + 1);
        self::assertSame([2, 3, 4], $results);
    }

    public function testConstructorWithExplicitMaxWorkers(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 3);
        $results = $executor->map(
            [10, 20, 30],
            fn (int $x): int => $x + 5
        );
        self::assertSame([15, 25, 35], $results);
    }

    public function testAutoDetectWorkerCountWithNullMaxWorkers(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend());
        $results = $executor->map(
            [1, 2, 3, 4],
            fn (int $x): int => $x * 10
        );
        self::assertSame([10, 20, 30, 40], $results);
    }

    public function testDefaultBackendDetection(): void
    {
        // Constructor with no arguments should auto-detect
        $executor = new ParallelExecutor();
        $results = $executor->map([1, 2], fn (int $x): int => $x);
        self::assertSame([1, 2], $results);
    }

    public function testPcntlBackendWithLargePayload(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $executor = new ParallelExecutor(new PcntlBackend(), 2);

        // Generate tasks that produce large serialized results
        $results = $executor->map(
            [1000, 2000],
            fn (int $size): string => str_repeat('x', $size)
        );

        /** @var list<string> $results */
        self::assertSame(1000, strlen($results[0]));
        self::assertSame(2000, strlen($results[1]));
    }

    public function testPcntlBackendTimeout(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/timed out/');

        // 1-second timeout, need 2+ tasks to avoid sequential shortcut
        $executor = new ParallelExecutor(new PcntlBackend(1), 2);
        $executor->map(
            [1, 2],
            function (int $x): int {
                sleep(10); // Will be killed after 1s

                return $x;
            }
        );
    }

    public function testAutoDetectWorkerCountWithMemoryConstraint(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        // With auto-detect (null maxWorkers), the memory limiter should still work
        $executor = new ParallelExecutor(new PcntlBackend());
        $results = $executor->map(
            [1, 2],
            fn (int $x): int => $x * 5
        );
        self::assertSame([5, 10], $results);
    }

    public function testWorkerCountLessThanTwoFallsBackToSequential(): void
    {
        // When maxWorkers=1 is explicitly set, should still work (sequential fallback)
        $executor = new ParallelExecutor(new SequentialBackend(), 1);
        $results = $executor->map(
            [10, 20, 30],
            fn (int $x): int => $x + 1
        );
        self::assertSame([11, 21, 31], $results);
    }

    public function testGetMemoryLimitBytesWithMegabytes(): void
    {
        // Exercise the public getMemoryLimitBytes method
        $bytes = ParallelExecutor::getMemoryLimitBytes();
        // memory_limit is set in test env, should return a positive value
        self::assertGreaterThan(0, $bytes);
    }

    public function testSequentialBackendFallbackWhenPcntlUnavailableConstruct(): void
    {
        // Construct with SequentialBackend explicitly
        $executor = new ParallelExecutor(new SequentialBackend(), 4);
        $results = $executor->map(
            [1, 2, 3],
            fn (int $x): int => $x * 3
        );
        self::assertSame([3, 6, 9], $results);
    }

    public function testGetMemoryLimitBytesWithNoLimit(): void
    {
        $oldLimit = ini_get('memory_limit');

        try {
            ini_set('memory_limit', '-1');
            self::assertSame(0, ParallelExecutor::getMemoryLimitBytes());
        } finally {
            ini_set('memory_limit', $oldLimit ?: '256M');
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('memoryLimitProvider')]
    public function testGetMemoryLimitBytesFormats(string $iniValue, int $expected): void
    {
        $oldLimit = ini_get('memory_limit');

        try {
            ini_set('memory_limit', $iniValue);
            self::assertSame($expected, ParallelExecutor::getMemoryLimitBytes());
        } finally {
            ini_set('memory_limit', $oldLimit ?: '256M');
        }
    }

    public static function memoryLimitProvider(): array
    {
        return [
            'kilobytes' => ['524288K', 524288 * 1024],
            'megabytes' => ['2048M', 2048 * 1024 * 1024],
            'gigabytes' => ['4G', 4 * 1024 * 1024 * 1024],
        ];
    }

    public function testApplyMemoryLimitWithNoLimitSet(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
        }

        $oldLimit = ini_get('memory_limit');

        try {
            ini_set('memory_limit', '-1');
            // With no memory limit, auto-detect should not constrain workers
            $executor = new ParallelExecutor(new PcntlBackend());
            $results = $executor->map(
                [1, 2, 3, 4],
                fn (int $x): int => $x * 2
            );
            self::assertSame([2, 4, 6, 8], $results);
        } finally {
            ini_set('memory_limit', $oldLimit ?: '256M');
        }
    }
}
