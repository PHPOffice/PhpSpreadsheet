<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Text
{
    /**
     * STRINGLENGTH.
     *
     * @param mixed $value String Value
     */
    public static function length($value = ''): int
    {
        $value = Functions::flattenSingleValue($value);

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_strlen($value, 'UTF-8');
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
        $value1 = Functions::flattenSingleValue($value1);
        $value2 = Functions::flattenSingleValue($value2);

        return ((string) $value2) === ((string) $value1);
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
