<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Operations
{
    use ArrayEnabled;

    /**
     * MOD.
     *
     * @param mixed $dividend Dividend
     *                      Or can be an array of values
     * @param mixed $divisor Divisor
     *                      Or can be an array of values
     *
     * @return array|float|int|string Remainder, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function mod($dividend, $divisor)
    {
        if (is_array($dividend) || is_array($divisor)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $dividend, $divisor);
        }

        try {
            $dividend = Helpers::validateNumericNullBool($dividend);
            $divisor = Helpers::validateNumericNullBool($divisor);
            Helpers::validateNotZero($divisor);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($dividend < 0.0) && ($divisor > 0.0)) {
            return $divisor - fmod(abs($dividend), $divisor);
        }
        if (($dividend > 0.0) && ($divisor < 0.0)) {
            return $divisor + fmod($dividend, abs($divisor));
        }

        return fmod($dividend, $divisor);
    }

    /**
     * POWER.
     *
     * Computes x raised to the power y.
     *
     * @param array|float|int $x
     *                      Or can be an array of values
     * @param array|float|int $y
     *                      Or can be an array of values
     *
     * @return array|float|int|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function power($x, $y)
    {
        if (is_array($x) || is_array($y)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $x, $y);
        }

        try {
            $x = Helpers::validateNumericNullBool($x);
            $y = Helpers::validateNumericNullBool($y);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if (!$x && !$y) {
            return ExcelError::NAN();
        }
        if (!$x && $y < 0.0) {
            return ExcelError::DIV0();
        }

        // Return
        $result = $x ** $y;

        return Helpers::numberOrNan($result);
    }

    /**
     * PRODUCT.
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function product(...$args)
    {
        $args = array_filter(
            Functions::flattenArray($args),
            function ($value) {
                return $value !== null;
            }
        );

        // Return value
        $returnValue = (count($args) === 0) ? 0.0 : 1.0;

        // Loop through arguments
        foreach ($args as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                $returnValue *= $arg;
            } else {
                return ExcelError::throwError($arg);
            }
        }

        return (float) $returnValue;
    }

    /**
     * QUOTIENT.
     *
     * QUOTIENT function returns the integer portion of a division. Numerator is the divided number
     *        and denominator is the divisor.
     *
     * Excel Function:
     *        QUOTIENT(value1,value2)
     *
     * @param mixed $numerator Expect float|int
     *                      Or can be an array of values
     * @param mixed $denominator Expect float|int
     *                      Or can be an array of values
     *
     * @return array|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function quotient($numerator, $denominator)
    {
        if (is_array($numerator) || is_array($denominator)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $numerator, $denominator);
        }

        try {
            $numerator = Helpers::validateNumericNullSubstitution($numerator, 0);
            $denominator = Helpers::validateNumericNullSubstitution($denominator, 0);
            Helpers::validateNotZero($denominator);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) ($numerator / $denominator);
    }
}
