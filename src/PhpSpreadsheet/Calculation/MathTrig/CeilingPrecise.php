<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

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
        MathTrig::nullFalseTrueToNumber($number);
        $significance = Functions::flattenSingleValue($significance);

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if ($significance == 0.0) {
                return 0.0;
            }
            $result = $number / abs($significance);

            return ceil($result) * $significance * (($significance < 0) ? -1 : 1);
        }

        return Functions::VALUE();
    }
}
