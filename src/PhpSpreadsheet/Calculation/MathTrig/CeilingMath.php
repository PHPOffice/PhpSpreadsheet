<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class CeilingMath
{
    /**
     * CEILING.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        CEILING.MATH(number[,significance[,mode]])
     *
     * @param mixed $number Number to round
     * @param mixed $significance Significance
     * @param int $mode direction to round negative numbers
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcCeilingMath($number, $significance = null, $mode = 0)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, ($number < 0) ? -1 : 1);
            $mode = Helpers::validateNumericNullSubstitution($mode, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (empty($significance * $number)) {
            return 0.0;
        }
        if (self::ceilingMathTest((float) $significance, (float) $number, (int) $mode)) {
            return floor($number / $significance) * $significance;
        }

        return ceil($number / $significance) * $significance;
    }

    /**
     * Let CEILINGMATH complexity pass Scrutinizer.
     */
    private static function ceilingMathTest(float $significance, float $number, int $mode): bool
    {
        return ((float) $significance < 0) || ((float) $number < 0 && !empty($mode));
    }
}
