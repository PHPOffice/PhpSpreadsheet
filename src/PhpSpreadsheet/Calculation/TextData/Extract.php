<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;

class Extract
{
    /**
     * LEFT.
     *
     * @param mixed $value String value from which to extract characters
     * @param mixed $chars The number of characters to extract (as an integer)
     */
    public static function left($value, $chars = 1): string
    {
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
     * @param mixed $start Integer offset of the first character that we want to extract
     * @param mixed $chars The number of characters to extract (as an integer)
     */
    public static function mid($value, $start, $chars): string
    {
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
     * @param mixed $chars The number of characters to extract (as an integer)
     */
    public static function right($value, $chars = 1): string
    {
        try {
            $value = Helpers::extractString($value);
            $chars = Helpers::extractInt($chars, 0, 1);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return mb_substr($value ?? '', mb_strlen($value ?? '', 'UTF-8') - $chars, $chars, 'UTF-8');
    }
}
