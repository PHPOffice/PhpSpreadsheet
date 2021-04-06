<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Coth
{
    /**
     * COTH.
     *
     * Returns the hyperbolic cotangent of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cotangent of the angle
     */
    public static function funcCoth($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(1.0, tanh($angle));
    }
}
