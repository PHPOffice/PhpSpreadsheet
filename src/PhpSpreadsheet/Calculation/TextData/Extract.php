<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;

class Extract
{
    use ArrayEnabled;

    /**
     * LEFT.
     *
     * @param mixed $value String value from which to extract characters
     *                         Or can be an array of values
     * @param mixed $chars The number of characters to extract (as an integer)
     *                         Or can be an array of values
     *
     * @return array|string The joined string
     *         If an array of values is passed for the $value or $chars arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function left($value, $chars = 1)
    {
        if (is_array($value) || is_array($chars)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $chars);
        }

        try {
            $value = Helpers::extractString($value);
            $chars = Helpers::extractInt($chars, 0, 1);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return mb_substr($value ?? '', 0, $chars, 'UTF-8');
    }

    /**
     * MID.
     *
     * @param mixed $value String value from which to extract characters
     *                         Or can be an array of values
     * @param mixed $start Integer offset of the first character that we want to extract
     *                         Or can be an array of values
     * @param mixed $chars The number of characters to extract (as an integer)
     *                         Or can be an array of values
     *
     * @return array|string The joined string
     *         If an array of values is passed for the $value, $start or $chars arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function mid($value, $start, $chars)
    {
        if (is_array($value) || is_array($start) || is_array($chars)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $start, $chars);
        }

        try {
            $value = Helpers::extractString($value);
            $start = Helpers::extractInt($start, 1);
            $chars = Helpers::extractInt($chars, 0);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return mb_substr($value ?? '', --$start, $chars, 'UTF-8');
    }

    /**
     * RIGHT.
     *
     * @param mixed $value String value from which to extract characters
     *                         Or can be an array of values
     * @param mixed $chars The number of characters to extract (as an integer)
     *                         Or can be an array of values
     *
     * @return array|string The joined string
     *         If an array of values is passed for the $value or $chars arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function right($value, $chars = 1)
    {
        if (is_array($value) || is_array($chars)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $chars);
        }

        try {
            $value = Helpers::extractString($value);
            $chars = Helpers::extractInt($chars, 0, 1);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return mb_substr($value ?? '', mb_strlen($value ?? '', 'UTF-8') - $chars, $chars, 'UTF-8');
    }
}
