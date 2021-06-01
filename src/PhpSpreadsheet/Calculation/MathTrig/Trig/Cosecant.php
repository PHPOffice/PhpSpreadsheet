<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Cosecant
{
    /**
     * CSC.
     *
     * Returns the cosecant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The cosecant of the angle
     */
    public static function csc($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, sin($angle));
    }

    /**
     * CSCH.
     *
     * Returns the hyperbolic cosecant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cosecant of the angle
     */
    public static function csch($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, sinh($angle));
    }
}
