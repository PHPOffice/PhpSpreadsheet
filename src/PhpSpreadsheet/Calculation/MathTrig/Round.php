<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Round
{
    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param mixed $number Should be numeric
     * @param mixed $precision Should be int
     *
     * @return float|string Rounded number
     */
    public static function builtinROUND($number, $precision)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $precision = Helpers::validateNumericNullBool($precision);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return round($number, (int) $precision);
    }
}
