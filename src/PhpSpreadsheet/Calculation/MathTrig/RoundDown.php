<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class RoundDown
{
    /**
     * ROUNDDOWN.
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcRoundDown($number, $digits)
    {
        MathTrig::nullFalseTrueToNumber($number);
        $digits = Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            if ($number == 0.0) {
                return 0.0;
            }

            if ($number < 0.0) {
                return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
            }

            return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
        }

        return Functions::VALUE();
    }
}
