<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Odd
{
    /**
     * ODD.
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @param float $number Number to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcOdd($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $significance = Helpers::returnSign($number);
        if ($significance == 0) {
            return 1;
        }

        $result = ceil($number / $significance) * $significance;
        if ($result == Helpers::getEven($result)) {
            $result += $significance;
        }

        return $result;
    }
}
