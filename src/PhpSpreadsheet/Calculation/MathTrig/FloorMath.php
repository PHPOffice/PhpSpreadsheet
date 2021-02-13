<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class FloorMath
{
    /**
     * FLOOR.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.MATH(number[,significance[,mode]])
     *
     * @param mixed $number Number to round
     * @param mixed $significance Significance
     * @param mixed $mode direction to round negative numbers
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcFloorMath($number, $significance = null, $mode = 0)
    {
        MathTrig::nullFalseTrueToNumber($number);
        $significance = Functions::flattenSingleValue($significance);
        $mode = Functions::flattenSingleValue($mode);

        if ($significance === null) {
            $significance = ((float) $number < 0) ? -1 : 1;
        }

        if (is_numeric($number) && is_numeric($significance) && is_numeric($mode)) {
            return self::argsOk((float) $number, (float) $significance, (int) $mode);
        }

        return Functions::VALUE();
    }

    /**
     * Avoid Scrutinizer complexity problems.
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    private static function argsOk(float $number, float $significance, int $mode)
    {
        if (!$significance) {
            return Functions::DIV0();
        }
        if (!$number) {
            return 0.0;
        }
        if (self::floorMathTest($number, $significance, $mode)) {
            return ceil($number / $significance) * $significance;
        }

        return floor($number / $significance) * $significance;
    }

    /**
     * Let FLOORMATH complexity pass Scrutinizer.
     */
    private static function floorMathTest(float $number, float $significance, int $mode): bool
    {
        return mathTrig::returnSign($significance) == -1 || (mathTrig::returnSign($number) == -1 && !empty($mode));
    }
}
