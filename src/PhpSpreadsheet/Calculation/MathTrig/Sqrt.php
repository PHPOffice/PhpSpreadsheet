<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Sqrt
{
    /**
     * SQRT.
     *
     * Returns the result of builtin function sqrt after validating args.
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

        return Helpers::numberOrNan(sqrt($number));
    }
}
