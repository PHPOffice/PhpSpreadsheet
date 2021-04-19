<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Acosh
{
    /**
     * ACOSH.
     *
     * Returns the arc hyperbolic cosine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccosine of the number
     */
    public static function funcAcosh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(acosh($number));
    }
}
