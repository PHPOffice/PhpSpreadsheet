<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Atan
{
    /**
     * ATAN.
     *
     * Returns the arctangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arctangent of the number
     */
    public static function funcAtan($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(atan($number));
    }
}
