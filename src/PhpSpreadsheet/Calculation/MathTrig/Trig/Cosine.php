<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Cosine
{
    /**
     * COS.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string cosine
     */
    public static function cos($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return cos($number);
    }

    /**
     * COSH.
     *
     * Returns the result of builtin function cosh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string hyperbolic cosine
     */
    public static function cosh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return cosh($number);
    }

    /**
     * ACOS.
     *
     * Returns the arccosine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccosine of the number
     */
    public static function acos($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(acos($number));
    }

    /**
     * ACOSH.
     *
     * Returns the arc inverse hyperbolic cosine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The inverse hyperbolic cosine of the number, or an error string
     */
    public static function acosh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(acosh($number));
    }
}
