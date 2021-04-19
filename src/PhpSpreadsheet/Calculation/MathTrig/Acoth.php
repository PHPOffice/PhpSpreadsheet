<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Acoth
{
    /**
     * ACOTH.
     *
     * Returns the hyperbolic arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The hyperbolic arccotangent of the number
     */
    public static function funcAcoth($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $result = ($number === 1) ? NAN : (log(($number + 1) / ($number - 1)) / 2);

        return Helpers::numberOrNan($result);
    }
}
