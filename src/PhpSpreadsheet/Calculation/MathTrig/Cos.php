<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Cos
{
    /**
     * COS.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string cosine
     */
    public static function funcCos($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return cos($number);
    }
}
