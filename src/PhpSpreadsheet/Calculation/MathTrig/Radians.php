<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Radians
{
    /**
     * RADIANS.
     *
     * Returns the result of builtin function deg2rad after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function evaluate($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return deg2rad($number);
    }
}
