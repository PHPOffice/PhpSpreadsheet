<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Csc
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
    public static function funcCsc($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, sin($angle));
    }
}
