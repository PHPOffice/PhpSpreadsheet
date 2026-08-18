<?php

namespace PhpOffice\PhpSpreadsheet\Parallel;

use Fidry\CpuCoreCounter\CpuCoreCounter;
use Fidry\CpuCoreCounter\NumberOfCpuCoreNotFound;

class CpuDetector
{
    private const FALLBACK_CPU_COUNT = 2;

    private static ?int $cachedCount = null;

    public static function detectCpuCount(): int
    {
        if (self::$cachedCount !== null) {
            return self::$cachedCount;
        }

        self::$cachedCount = static::detect();

        return self::$cachedCount;
    }

    /**
     * Reset cached value (for testing).
     */
    public static function reset(): void
    {
        self::$cachedCount = null;
    }

    protected static function detect(): int
    {
        try {
            return (new CpuCoreCounter())->getCount();
            // @codeCoverageIgnoreStart
        } catch (NumberOfCpuCoreNotFound) {
            return self::FALLBACK_CPU_COUNT;
        }
        // @codeCoverageIgnoreEnd
    }
}
