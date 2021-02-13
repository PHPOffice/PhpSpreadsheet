<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class FloorPrecise
{
    /**
     * FLOOR.PRECISE.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.PRECISE(number[,significance])
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcFloorPrecise($number, $significance = 1)
    {
        MathTrig::nullFalseTrueToNumber($number);
        $significance = Functions::flattenSingleValue($significance);

        if ((is_numeric($number)) && (is_numeric($significance))) {
            return self::argumentsOk((float) $number, (float) $significance);
        }

        return Functions::VALUE();
    }

    /**
     * Avoid Scrutinizer problems concerning complexity.
     *
     * @return float|string
     */
    private static function argumentsOk(float $number, float $significance)
    {
        if ($significance == 0.0) {
            return Functions::DIV0();
        }
        if ($number == 0.0) {
            return 0.0;
        }

        return floor($number / abs($significance)) * abs($significance);
    }
}
