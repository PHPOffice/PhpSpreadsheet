<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Extract
{
    /**
     * LEFT.
     *
     * @param string $value Value
     * @param int $chars Number of characters
     */
    public static function left($value = '', $chars = 1): string
    {
        $value = Functions::flattenSingleValue($value);
        $chars = Functions::flattenSingleValue($chars);

        if (!is_numeric($chars) || $chars < 0) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_substr($value, 0, $chars, 'UTF-8');
    }

    /**
     * MID.
     *
     * @param string $value Value
     * @param int $start Start character
     * @param int $chars Number of characters
     */
    public static function mid($value = '', $start = 1, $chars = null): string
    {
        $value = Functions::flattenSingleValue($value);
        $start = Functions::flattenSingleValue($start);
        $chars = Functions::flattenSingleValue($chars);

        if (!is_numeric($start) || $start < 1 || !is_numeric($chars) || $chars < 0) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_substr($value, --$start, $chars, 'UTF-8');
    }

    /**
     * RIGHT.
     *
     * @param string $value Value
     * @param int $chars Number of characters
     */
    public static function right($value = '', $chars = 1): string
    {
        $value = Functions::flattenSingleValue($value);
        $chars = Functions::flattenSingleValue($chars);

        if (!is_numeric($chars) || $chars < 0) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_substr($value, mb_strlen($value, 'UTF-8') - $chars, $chars, 'UTF-8');
    }
}
