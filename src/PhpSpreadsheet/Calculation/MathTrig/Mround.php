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
     * @param mixed $number Expect float. Number to round.
     * @param mixed $multiple Expect int. Multiple to which you want to round.
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function evaluate($number, $multiple)
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
