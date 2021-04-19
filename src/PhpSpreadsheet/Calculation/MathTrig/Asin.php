<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Asin
{
    /**
     * ASIN.
     *
     * Returns the arcsine of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arcsine of the number
     */
    public static function funcAsin($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asin($number));
    }
}
