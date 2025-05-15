<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Floor
{
    use ArrayEnabled;

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
     *                      Or can be an array of values
     * @param mixed $significance Expect float. Significance
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function floor(mixed $number, mixed $significance = null)
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
     * FLOOR.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.MATH(number[,significance[,mode]])
     *
     * @param mixed $number Number to round
     *                      Or can be an array of values
     * @param mixed $significance Significance
     *                      Or can be an array of values
     * @param mixed $mode direction to round negative numbers
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function math(mixed $number, mixed $significance = null, mixed $mode = 0, bool $checkSigns = false)
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
        if ($checkSigns) {
            if (($number > 0 && $significance < 0) || ($number < 0 && $significance > 0)) {
                return ExcelError::NAN();
            }
        }

        return self::argsOk((float) $number, (float) $significance, (int) $mode);
    }

    /**
     * FLOOR.ODS, pseudo-function - FLOOR as implemented in ODS.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * ODS Function (theoretical):
     *        FLOOR.ODS(number[,significance[,mode]])
     *
     * @param mixed $number Number to round
     * @param mixed $significance Significance
     * @param array<mixed>|int $mode direction to round negative numbers
     *
     * @return array<mixed>|float|string Rounded Number, or a string containing an error
     */
    public static function mathOds(mixed $number, mixed $significance = null, mixed $mode = 0)
    {
        return self::math($number, $significance, $mode, true);
    }

    /**
     * FLOOR.PRECISE.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.PRECISE(number[,significance])
     *
     * @param array<mixed>|float $number Number to round
     *                      Or can be an array of values
     * @param array<mixed>|float $significance Significance
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string Rounded Number, or a string containing an error
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

        return self::argumentsOkPrecise((float) $number, (float) $significance);
    }

    /**
     * Avoid Scrutinizer problems concerning complexity.
     */
    private static function argumentsOkPrecise(float $number, float $significance): string|float
    {
        if ($significance == 0.0) {
            return ExcelError::DIV0();
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
    private static function argsOk(float $number, float $significance, int $mode): string|float
    {
        if (!$significance) {
            return ExcelError::DIV0();
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
     */
    private static function argumentsOk(float $number, float $significance): string|float
    {
        if ($significance == 0.0) {
            return ExcelError::DIV0();
        }
        if ($number == 0.0) {
            return 0.0;
        }
        $signSig = Helpers::returnSign($significance);
        $signNum = Helpers::returnSign($number);
        if (
            ($signSig === 1 && ($signNum === 1 || Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_GNUMERIC))
            || ($signNum === -1 && $signSig === -1)
        ) {
            return floor($number / $significance) * $significance;
        }

        return ExcelError::NAN();
    }
}
