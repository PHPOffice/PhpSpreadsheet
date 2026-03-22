<?php

namespace PhpOffice\PhpSpreadsheet\Parallel;

class CpuDetector
{
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
        return static::fromPcntl()
            ?? static::fromEnv()
            ?? static::fromProcCpuinfo()
            ?? static::fromSysctl()
            ?? static::fromNproc()
            ?? 2;
    }

    protected static function fromPcntl(): ?int
    {
        if (!function_exists('pcntl_cpu_count')) {
            return null;
        }

        // @codeCoverageIgnoreStart
        /** @phpstan-ignore argument.type */
        $result = call_user_func('pcntl_cpu_count');
        $count = is_int($result) ? $result : 0;

        return $count > 0 ? $count : null;
        // @codeCoverageIgnoreEnd
    }

    protected static function fromEnv(): ?int
    {
        $env = getenv('NUMBER_OF_PROCESSORS');
        if ($env === false) {
            return null;
        }

        $count = (int) $env;

        return $count > 0 ? $count : null;
    }

    protected static function fromProcCpuinfo(): ?int
    {
        if (!is_readable('/proc/cpuinfo')) {
            return null; // @codeCoverageIgnore
        }

        $cpuinfo = @file_get_contents('/proc/cpuinfo');
        if ($cpuinfo === false) {
            return null; // @codeCoverageIgnore
        }

        $count = substr_count($cpuinfo, 'processor');

        return $count > 0 ? $count : null;
    }

    /**
     * @codeCoverageIgnore Platform-specific: macOS only
     */
    protected static function fromSysctl(): ?int
    {
        if (PHP_OS_FAMILY !== 'Darwin') {
            return null;
        }

        $result = static::shellExec('sysctl -n hw.logicalcpu 2>/dev/null');
        if ($result === null) {
            return null;
        }

        $count = (int) trim($result);

        return $count > 0 ? $count : null;
    }

    protected static function fromNproc(): ?int
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return null; // @codeCoverageIgnore
        }

        $result = static::shellExec('nproc 2>/dev/null');
        if ($result === null) {
            return null; // @codeCoverageIgnore
        }

        $count = (int) trim($result);

        return $count > 0 ? $count : null;
    }

    protected static function shellExec(string $command): ?string
    {
        if (!function_exists('shell_exec')) {
            return null; // @codeCoverageIgnore
        }

        return @shell_exec($command); // @phpstan-ignore return.type
    }
}
