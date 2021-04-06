<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Asinh
{
    /**
     * ASINH.
     *
     * Returns the arc hyperbolic sine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arc hyperbolic sine of the number
     */
    public static function funcAsinh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asinh($number));
    }
}
