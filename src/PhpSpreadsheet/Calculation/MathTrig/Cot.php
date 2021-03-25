<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Cot
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
    public static function funcCot($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(cos($angle), sin($angle));
    }
}
