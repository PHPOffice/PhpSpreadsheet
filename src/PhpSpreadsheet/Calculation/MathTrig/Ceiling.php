<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Ceiling
{
    use ArrayEnabled;

    /**
     * CEILING.
     *
     * Returns number rounded up, away from zero, to the nearest multiple of significance.
     *        For example, if you want to avoid using pennies in your prices and your product is
     *        priced at $4.42, use the formula =CEILING(4.42,0.05) to round prices up to the
     *        nearest nickel.
     *
     * Excel Function:
     *        CEILING(number[,significance])
     *
     * @param array|float $number the number you want the ceiling
     *                      Or can be an array of values
     * @param array|float $significance the multiple to which you want to round
     *                      Or can be an array of values
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function ceiling($number, $significance = null)
    {
        if (is_array($number) || is_array($significance)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $significance);
        }

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
     * CEILING.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        CEILING.MATH(number[,significance[,mode]])
     *
     * @param mixed $number Number to round
     *                      Or can be an array of values
     * @param mixed $significance Significance
     *                      Or can be an array of values
     * @param array|int $mode direction to round negative numbers
     *                      Or can be an array of values
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function math($number, $significance = null, $mode = 0)
    {
        if (is_array($number) || is_array($significance) || is_array($mode)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $significance, $mode);
        }

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
     * CEILING.PRECISE.
     *
     * Rounds number up, away from zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        CEILING.PRECISE(number[,significance])
     *
     * @param mixed $number the number you want to round
     *                      Or can be an array of values
     * @param array|float $significance the multiple to which you want to round
     *                      Or can be an array of values
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function precise($number, $significance = 1)
    {
        if (is_array($number) || is_array($significance)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $significance);
        }

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

    /**
     * Let CEILINGMATH complexity pass Scrutinizer.
     */
    private static function ceilingMathTest(float $significance, float $number, int $mode): bool
    {
        return ((float) $significance < 0) || ((float) $number < 0 && !empty($mode));
    }

    /**
     * Avoid Scrutinizer problems concerning complexity.
     *
     * @return float|string
     */
    private static function argumentsOk(float $number, float $significance)
    {
        if (empty($number * $significance)) {
            return 0.0;
        }
        if (Helpers::returnSign($number) == Helpers::returnSign($significance)) {
            return ceil($number / $significance) * $significance;
        }

        return ExcelError::NAN();
    }

    private static function floorCheck1Arg(): void
    {
        $compatibility = Functions::getCompatibilityMode();
        if ($compatibility === Functions::COMPATIBILITY_EXCEL) {
            throw new Exception('Excel requires 2 arguments for CEILING');
        }
    }
}
