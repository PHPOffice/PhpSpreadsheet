<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
        try {
            $number = Helpers::validateNumericNullSubstitution($number, 0);
            $multiple = Helpers::validateNumericNullSubstitution($multiple, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($number == 0 || $multiple == 0) {
            return 0;
        }
        if ((Helpers::returnSign($number)) == (Helpers::returnSign($multiple))) {
            $multiplier = 1 / $multiple;

            return round($number * $multiplier) / $multiplier;
        }

        return Functions::NAN();
    }
}
