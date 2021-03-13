<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Acot
{
    /**
     * ACOT.
     *
     * Returns the arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccotangent of the number
     */
    public static function funcAcot($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (M_PI / 2) - atan($number);
    }
}
