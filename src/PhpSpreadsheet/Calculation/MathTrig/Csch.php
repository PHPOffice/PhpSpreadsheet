<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Csch
{
    /**
     * CSCH.
     *
     * Returns the hyperbolic cosecant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cosecant of the angle
     */
    public static function funcCsch($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, sinh($angle));
    }
}
