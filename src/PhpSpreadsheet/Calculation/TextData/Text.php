<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Text
{
    /**
     * LEN.
     *
     * @param mixed $value String Value
     */
    public static function length($value = ''): int
    {
        $value = Helpers::extractString($value);

        return mb_strlen($value ?? '', 'UTF-8');
    }

    /**
     * Compares two text strings and returns TRUE if they are exactly the same, FALSE otherwise.
     * EXACT is case-sensitive but ignores formatting differences.
     * Use EXACT to test text being entered into a document.
     *
     * @param mixed $value1 String Value
     * @param mixed $value2 String Value
     */
    public static function exact($value1, $value2): bool
    {
        $value1 = Helpers::extractString($value1);
        $value2 = Helpers::extractString($value2);

        return $value2 === $value1;
    }

    /**
     * RETURNSTRING.
     *
     * @param mixed $testValue Value to check
     *
     * @return null|string
     */
    public static function test($testValue = '')
    {
        $testValue = Functions::flattenSingleValue($testValue);

        if (is_string($testValue)) {
            return $testValue;
        }

        return null;
    }
}
