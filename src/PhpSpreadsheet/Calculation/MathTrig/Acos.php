<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Acos
{
    /**
     * ACOS.
     *
     * Returns the arccosine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccosine of the number
     */
    public static function funcAcos($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(acos($number));
    }
}
