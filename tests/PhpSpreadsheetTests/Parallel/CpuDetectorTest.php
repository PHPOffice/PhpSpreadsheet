<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\CpuDetector;
use PHPUnit\Framework\TestCase;

class CpuDetectorTest extends TestCase
{
    protected function setUp(): void
    {
        CpuDetector::reset();
        TestableCpuDetector::reset();
        FallbackCpuDetector::reset();
    }

    protected function tearDown(): void
    {
        CpuDetector::reset();
        TestableCpuDetector::reset();
        FallbackCpuDetector::reset();
    }

    public function testDetectCpuCountReturnsPositiveInt(): void
    {
        $count = CpuDetector::detectCpuCount();
        self::assertGreaterThan(0, $count);
    }

    public function testDetectCpuCountIsCached(): void
    {
        $first = CpuDetector::detectCpuCount();
        $second = CpuDetector::detectCpuCount();
        self::assertSame($first, $second);
    }

    public function testResetClearsCache(): void
    {
        $first = CpuDetector::detectCpuCount();
        CpuDetector::reset();
        $second = CpuDetector::detectCpuCount();
        self::assertSame($first, $second);
    }

    public function testFromPcntlReturnsPositiveOrNull(): void
    {
        $result = TestableCpuDetector::testFromPcntl();
        if (function_exists('pcntl_cpu_count')) {
            self::assertIsInt($result);
            self::assertGreaterThan(0, $result);
        } else {
            self::assertNull($result);
        }
    }

    public function testFromEnvReturnsNullWhenNotSet(): void
    {
        $old = getenv('NUMBER_OF_PROCESSORS');
        putenv('NUMBER_OF_PROCESSORS');

        try {
            self::assertNull(TestableCpuDetector::testFromEnv());
        } finally {
            if ($old !== false) {
                putenv("NUMBER_OF_PROCESSORS={$old}");
            }
        }
    }

    public function testFromEnvReturnsParsedValue(): void
    {
        $old = getenv('NUMBER_OF_PROCESSORS');
        putenv('NUMBER_OF_PROCESSORS=8');

        try {
            self::assertSame(8, TestableCpuDetector::testFromEnv());
        } finally {
            if ($old !== false) {
                putenv("NUMBER_OF_PROCESSORS={$old}");
            } else {
                putenv('NUMBER_OF_PROCESSORS');
            }
        }
    }

    public function testFromEnvReturnsNullForZero(): void
    {
        $old = getenv('NUMBER_OF_PROCESSORS');
        putenv('NUMBER_OF_PROCESSORS=0');

        try {
            self::assertNull(TestableCpuDetector::testFromEnv());
        } finally {
            if ($old !== false) {
                putenv("NUMBER_OF_PROCESSORS={$old}");
            } else {
                putenv('NUMBER_OF_PROCESSORS');
            }
        }
    }

    public function testFromProcCpuinfo(): void
    {
        $result = TestableCpuDetector::testFromProcCpuinfo();
        if (is_readable('/proc/cpuinfo')) {
            self::assertIsInt($result);
            self::assertGreaterThan(0, $result);
        } else {
            self::assertNull($result);
        }
    }

    public function testFromSysctl(): void
    {
        $result = TestableCpuDetector::testFromSysctl();
        if (PHP_OS_FAMILY === 'Darwin') {
            self::assertIsInt($result);
            self::assertGreaterThan(0, $result);
        } else {
            self::assertNull($result);
        }
    }

    public function testFromNproc(): void
    {
        $result = TestableCpuDetector::testFromNproc();
        if (PHP_OS_FAMILY === 'Windows') {
            self::assertNull($result);
        } else {
            // nproc may not be available on all Unix systems (e.g., macOS without coreutils)
            self::assertTrue($result === null || $result > 0);
        }
    }

    public function testFallbackWhenAllStrategiesReturnNull(): void
    {
        $count = FallbackCpuDetector::detectCpuCount();
        self::assertSame(2, $count);
    }
}
