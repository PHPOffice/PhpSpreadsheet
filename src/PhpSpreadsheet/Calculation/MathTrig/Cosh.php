<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Cosh
{
    /**
     * COSH.
     *
     * Returns the result of builtin function cosh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string cosine
     */
    public static function funcCosh($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return cosh($number);
    }
}
