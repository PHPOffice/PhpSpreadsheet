<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class CeilingPrecise
{
    /**
     * CEILING.PRECISE.
     *
     * Rounds number up, away from zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        CEILING.PRECISE(number[,significance])
     *
     * @param mixed $number the number you want to round
     * @param float $significance the multiple to which you want to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcCeilingPrecise($number, $significance = 1)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (!$significance) {
            return 0.0;
        }
        $result = $number / abs($significance);

        return ceil($result) * $significance * (($significance < 0) ? -1 : 1);
    }
}
