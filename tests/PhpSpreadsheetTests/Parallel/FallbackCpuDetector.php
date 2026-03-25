<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\CpuDetector;

/**
 * Test subclass that forces all strategies to return null, exercising the fallback path.
 */
class FallbackCpuDetector extends CpuDetector
{
    protected static function fromPcntl(): ?int
    {
        return null;
    }

    protected static function fromEnv(): ?int
    {
        return null;
    }

    protected static function fromProcCpuinfo(): ?int
    {
        return null;
    }

    protected static function fromSysctl(): ?int
    {
        return null;
    }

    protected static function fromNproc(): ?int
    {
        return null;
    }
}
