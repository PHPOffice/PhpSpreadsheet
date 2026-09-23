<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use RuntimeException;

/** Stream wrapper used to simulate partial writes and exhausted storage. */
class ControlledStream
{
    /** @var null|resource */
    public $context;

    public static string $data = '';

    public static int $writeSize = 7;

    public static ?int $bytesUntilFailure = null;

    public static bool $returnFalse = false;

    public static bool $throwOnFailure = false;

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- PHP stream wrapper API
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        return true;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- PHP stream wrapper API
    public function stream_write(string $data): int|false
    {
        if (self::$bytesUntilFailure === 0) {
            if (self::$throwOnFailure) {
                throw new RuntimeException('Simulated write exception.');
            }

            return self::$returnFalse ? false : 0;
        }
        $length = min(strlen($data), self::$writeSize, self::$bytesUntilFailure ?? PHP_INT_MAX);
        self::$data .= substr($data, 0, $length);
        if (self::$bytesUntilFailure !== null) {
            self::$bytesUntilFailure -= $length;
        }

        return $length;
    }
}
