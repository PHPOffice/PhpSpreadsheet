<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Round
{
    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param mixed $number Should be numeric
     * @param mixed $precision Should be int
     *
     * @return float|string Rounded number
     */
    public static function round($number, $precision)
    {
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
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function up($number, $digits)
    {
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
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function down($number, $digits)
    {
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
     * @param mixed $number Expect float. Number to round.
     * @param mixed $multiple Expect int. Multiple to which you want to round.
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function multiple($number, $multiple)
    {
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

        return Functions::NAN();
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
     * @param float $number Number to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function even($number)
    {
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
     * @param float $number Number to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function odd($number)
    {
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
