<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Sine
{
    use ArrayEnabled;

    /**
     * SIN.
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @param mixed $angle Should be numeric, or can be an array of numbers
     *
     * @return array<mixed>|float|string sine
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function sin(mixed $angle): array|string|float
    {
        if (is_array($angle)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $angle);
        }

        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sin($angle);
    }

    /**
     * SINH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $angle Should be numeric, or can be an array of numbers
     *
     * @return array<mixed>|float|string hyperbolic sine
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function sinh(mixed $angle): array|string|float
    {
        if (is_array($angle)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $angle);
        }

        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return sinh($angle);
    }

    /**
     * ASIN.
     *
     * Returns the arcsine of a number.
     *
     * @param array<mixed>|float $number Number, or can be an array of numbers
     *
     * @return array<mixed>|float|string The arcsine of the number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function asin($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asin($number));
    }

    /**
     * ASINH.
     *
     * Returns the inverse hyperbolic sine of a number.
     *
     * @param array<mixed>|float $number Number, or can be an array of numbers
     *
     * @return array<mixed>|float|string The inverse hyperbolic sine of the number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function asinh($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(asinh($number));
    }
}
