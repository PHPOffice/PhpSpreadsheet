<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Mround
{
    /**
     * MROUND.
     *
     * Rounds a number to the nearest multiple of a specified value
     *
     * @param float $number Number to round
     * @param int $multiple Multiple to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcMround($number, $multiple)
    {
        $number = Functions::flattenSingleValue($number);
        $number = $number ?? 0;

        $multiple = Functions::flattenSingleValue($multiple);

        if ((is_numeric($number)) && (is_numeric($multiple))) {
            if ($number == 0 || $multiple == 0) {
                return 0;
            }
            if ((MathTrig::SIGN($number)) == (MathTrig::SIGN($multiple))) {
                $multiplier = 1 / $multiple;

                return round($number * $multiplier) / $multiplier;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }
}
