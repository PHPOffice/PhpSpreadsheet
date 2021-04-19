<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Sec
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
    public static function funcSec($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, cos($angle));
    }
}
