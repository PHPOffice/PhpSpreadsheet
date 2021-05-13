<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Secant
{
    /**
     * SEC.
     *
     * Returns the secant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The secant of the angle
     */
    public static function sec($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, cos($angle));
    }

    /**
     * SECH.
     *
     * Returns the hyperbolic secant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic secant of the angle
     */
    public static function sech($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, cosh($angle));
    }
}
