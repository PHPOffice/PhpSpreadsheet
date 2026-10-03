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

    /**
     * Whether the optional fidry/cpu-core-counter package is installed.
     */
    public static function isAvailable(): bool
    {
        return class_exists(CpuCoreCounter::class);
    }

    protected static function detect(): int
    {
        if (!self::isAvailable()) {
            return self::FALLBACK_CPU_COUNT; // @codeCoverageIgnore
        }

        try {
            return (new CpuCoreCounter())->getCount();
            // @codeCoverageIgnoreStart
        } catch (NumberOfCpuCoreNotFound) {
            return self::FALLBACK_CPU_COUNT;
        }
        // @codeCoverageIgnoreEnd
    }
}
