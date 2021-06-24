<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Sqrt
{
    /**
     * SQRT.
     *
     * Returns the result of builtin function sqrt after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string square roor
     */
    public static function sqrt($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(sqrt($number));
    }

    /**
     * SQRTPI.
     *
     * Returns the square root of (number * pi).
     *
     * @param float $number Number
     *
     * @return float|string Square Root of Number * Pi, or a string containing an error
     */
    public static function pi($number)
    {
        try {
            $number = Helpers::validateNumericNullSubstitution($number, 0);
            Helpers::validateNotNegative($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sqrt($number * M_PI);
    }
}
