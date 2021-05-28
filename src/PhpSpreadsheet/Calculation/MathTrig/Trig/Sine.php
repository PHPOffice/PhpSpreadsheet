<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Sine
{
    /**
     * SIN.
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string sine
     */
    public static function sin($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sin($angle);
    }

    /**
     * SINH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string hyperbolic sine
     */
    public static function sinh($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sinh($angle);
    }

    /**
     * ASIN.
     *
     * Returns the arcsine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arcsine of the number
     */
    public static function asin($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asin($number));
    }

    /**
     * ASINH.
     *
     * Returns the inverse hyperbolic sine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The inverse hyperbolic sine of the number
     */
    public static function asinh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asinh($number));
    }
}
