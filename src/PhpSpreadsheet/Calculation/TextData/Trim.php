<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;

class Trim
{
    use ArrayEnabled;

    /**
     * CLEAN.
     *
     * @param mixed $stringValue String Value to check
     *                              Or can be an array of values
     *
     * @return array<mixed>|string If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function nonPrintable(mixed $stringValue = '')
    {
        if (is_array($stringValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $stringValue);
        }

        $stringValue = Helpers::extractString($stringValue);

        return (string) preg_replace('/[\x00-\x1f]/', '', "$stringValue");
    }

    /**
     * TRIM.
     *
     * @param mixed $stringValue String Value to check
     *                              Or can be an array of values
     *
     * @return array<mixed>|string If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function spaces(mixed $stringValue = ''): array|string
    {
        if (is_array($stringValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $stringValue);
        }

        $stringValue = Helpers::extractString($stringValue);

        return trim(preg_replace('/ +/', ' ', trim("$stringValue", ' ')) ?? '', ' ');
    }
}
