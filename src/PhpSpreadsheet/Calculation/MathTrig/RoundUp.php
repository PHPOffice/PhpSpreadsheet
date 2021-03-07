<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class RoundUp
{
    /**
     * ROUNDUP.
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcRoundUp($number, $digits)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $digits = Helpers::validateNumericNullSubstitution($digits, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($number == 0.0) {
            return 0.0;
        }

        if ($number < 0.0) {
            return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
        }

        return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
    }
}
