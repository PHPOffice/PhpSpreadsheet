<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\CpuDetector;

/**
 * Test subclass that exposes protected methods for direct testing.
 */
class TestableCpuDetector extends CpuDetector
{
    public static function testFromPcntl(): ?int
    {
        return parent::fromPcntl();
    }

    public static function testFromEnv(): ?int
    {
        return parent::fromEnv();
    }

    public static function testFromProcCpuinfo(): ?int
    {
        return parent::fromProcCpuinfo();
    }

    public static function testFromSysctl(): ?int
    {
        return parent::fromSysctl();
    }

    public static function testFromNproc(): ?int
    {
        return parent::fromNproc();
    }
}
