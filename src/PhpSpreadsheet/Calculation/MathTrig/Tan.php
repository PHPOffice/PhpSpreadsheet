<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Tan
{
    /**
     * TAN.
     *
     * Returns the result of builtin function tan after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function funcTan($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(sin($angle), cos($angle));
    }
}
