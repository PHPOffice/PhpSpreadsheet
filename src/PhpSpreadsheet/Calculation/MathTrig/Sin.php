<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Sin
{
    /**
     * SIN.
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function funcSin($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sin($angle);
    }
}
