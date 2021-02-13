<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Floor
{
    private static function floorCheck1Arg(): void
    {
        $compatibility = Functions::getCompatibilityMode();
        if ($compatibility === Functions::COMPATIBILITY_EXCEL) {
            throw new Exception('Excel requires 2 arguments for FLOOR');
        }
    }

    /**
     * FLOOR.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR(number[,significance])
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcFloor($number, $significance = null)
    {
        MathTrig::nullFalseTrueToNumber($number);
        $significance = Functions::flattenSingleValue($significance);

        if ($significance === null) {
            self::floorCheck1Arg();
            $significance = MathTrig::returnSign((float) $number);
        }

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
        if (MathTrig::returnSign($significance) == 1) {
            return floor($number / $significance) * $significance;
        }
        if (MathTrig::returnSign($number) == -1 && MathTrig::returnSign($significance) == -1) {
            return floor($number / $significance) * $significance;
        }

        return Functions::NAN();
    }
}
