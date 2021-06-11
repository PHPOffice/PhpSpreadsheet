<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Cotangent
{
    /**
     * COT.
     *
     * Returns the cotangent of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The cotangent of the angle
     */
    public static function cot($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(cos($angle), sin($angle));
    }

    /**
     * COTH.
     *
     * Returns the hyperbolic cotangent of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cotangent of the angle
     */
    public static function coth($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, tanh($angle));
    }

    /**
     * ACOT.
     *
     * Returns the arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccotangent of the number
     */
    public static function acot($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (M_PI / 2) - atan($number);
    }

    /**
     * ACOTH.
     *
     * Returns the hyperbolic arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The hyperbolic arccotangent of the number
     */
    public static function acoth($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $result = ($number === 1) ? NAN : (log(($number + 1) / ($number - 1)) / 2);

        return Helpers::numberOrNan($result);
    }
}
