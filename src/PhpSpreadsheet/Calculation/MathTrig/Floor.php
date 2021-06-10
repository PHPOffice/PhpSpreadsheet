<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
     * @param mixed $number Expect float. Number to round
     * @param mixed $significance Expect float. Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function floor($number, $significance = null)
    {
        if ($significance === null) {
            self::floorCheck1Arg();
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, ($number < 0) ? -1 : 1);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::argumentsOk((float) $number, (float) $significance);
    }

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
    public static function math($number, $significance = null, $mode = 0)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, ($number < 0) ? -1 : 1);
            $mode = Helpers::validateNumericNullSubstitution($mode, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::argsOk((float) $number, (float) $significance, (int) $mode);
    }

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
    public static function precise($number, $significance = 1)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::argumentsOkPrecise((float) $number, (float) $significance);
    }

    /**
     * Avoid Scrutinizer problems concerning complexity.
     *
     * @return float|string
     */
    private static function argumentsOkPrecise(float $number, float $significance)
    {
        if ($significance == 0.0) {
            return Functions::DIV0();
        }
        if ($number == 0.0) {
            return 0.0;
        }

        return floor($number / abs($significance)) * abs($significance);
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
        return Helpers::returnSign($significance) == -1 || (Helpers::returnSign($number) == -1 && !empty($mode));
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
        if (Helpers::returnSign($significance) == 1) {
            return floor($number / $significance) * $significance;
        }
        if (Helpers::returnSign($number) == -1 && Helpers::returnSign($significance) == -1) {
            return floor($number / $significance) * $significance;
        }

        return Functions::NAN();
    }
}
