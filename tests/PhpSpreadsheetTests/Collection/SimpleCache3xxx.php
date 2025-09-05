<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Collection;

use DateInterval;
use PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache3;

class SimpleCache3xxx extends SimpleCache3
{
    public static int $useCount = 0;

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        ++self::$useCount;
        if (self::$useCount >= 4) {
            return false;
        }

        return parent::set($key, $value, $ttl);
    }
}
