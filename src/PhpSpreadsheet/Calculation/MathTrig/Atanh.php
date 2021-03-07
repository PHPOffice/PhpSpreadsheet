<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Atanh
{
    /**
     * ATANH.
     *
     * Returns the arc hyperbolic tangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arc hyperbolic tangent of the number
     */
    public static function funcAtanh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(atanh($number));
    }
}
