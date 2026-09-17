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
    }

    protected function tearDown(): void
    {
        CpuDetector::reset();
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
}
