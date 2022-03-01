<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Round
{
    use ArrayEnabled;

    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param mixed $number Should be numeric, or can be an array of numbers
     * @param mixed $precision Should be int, or can be an array of numbers
     *
     * @return array|float|string Rounded number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function round($number, $precision)
    {
        if (is_array($number) || is_array($precision)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $precision);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
            $precision = Helpers::validateNumericNullBool($precision);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return round($number, (int) $precision);
    }

    /**
     * ROUNDUP.
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @param array|float $number Number to round, or can be an array of numbers
     * @param array|int $digits Number of digits to which you want to round $number, or can be an array of numbers
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function up($number, $digits)
    {
        if (is_array($number) || is_array($digits)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $digits);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
            $digits = (int) Helpers::validateNumericNullSubstitution($digits, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($number == 0.0) {
            return 0.0;
        }

        if ($number < 0.0) {
            return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
        }

        return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
    }

    /**
     * ROUNDDOWN.
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @param array|float $number Number to round, or can be an array of numbers
     * @param array|int $digits Number of digits to which you want to round $number, or can be an array of numbers
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function down($number, $digits)
    {
        if (is_array($number) || is_array($digits)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $digits);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
            $digits = (int) Helpers::validateNumericNullSubstitution($digits, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($number == 0.0) {
            return 0.0;
        }

        if ($number < 0.0) {
            return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
        }

        return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
    }

    /**
     * MROUND.
     *
     * Rounds a number to the nearest multiple of a specified value
     *
     * @param mixed $number Expect float. Number to round, or can be an array of numbers
     * @param mixed $multiple Expect int. Multiple to which you want to round, or can be an array of numbers.
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function multiple($number, $multiple)
    {
        if (is_array($number) || is_array($multiple)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $multiple);
        }

        try {
            $number = Helpers::validateNumericNullSubstitution($number, 0);
            $multiple = Helpers::validateNumericNullSubstitution($multiple, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($number == 0 || $multiple == 0) {
            return 0;
        }
        if ((Helpers::returnSign($number)) == (Helpers::returnSign($multiple))) {
            $multiplier = 1 / $multiple;

            return round($number * $multiplier) / $multiplier;
        }

        return ExcelError::NAN();
    }

    /**
     * EVEN.
     *
     * Returns number rounded up to the nearest even integer.
     * You can use this function for processing items that come in twos. For example,
     *        a packing crate accepts rows of one or two items. The crate is full when
     *        the number of items, rounded up to the nearest two, matches the crate's
     *        capacity.
     *
     * Excel Function:
     *        EVEN(number)
     *
     * @param array|float $number Number to round, or can be an array of numbers
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function even($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::getEven($number);
    }

    /**
     * ODD.
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @param array|float $number Number to round, or can be an array of numbers
     *
     * @return array|float|string Rounded Number, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function odd($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

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
