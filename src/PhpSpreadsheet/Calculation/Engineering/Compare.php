<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Compare
{
    use ArrayEnabled;

    /**
     * DELTA.
     *
     *    Excel Function:
     *        DELTA(a[,b])
     *
     *    Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise.
     *    Use this function to filter a set of values. For example, by summing several DELTA
     *        functions you calculate the count of equal pairs. This function is also known as the
     *        Kronecker Delta function.
     *
     * @param array|float $a the first number
     *                      Or can be an array of values
     * @param array|float $b The second number. If omitted, b is assumed to be zero.
     *                      Or can be an array of values
     *
     * @return array|int|string (string in the event of an error)
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function DELTA($a, $b = 0.0)
    {
        if (is_array($a) || is_array($b)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $a, $b);
        }

        try {
            $a = EngineeringValidations::validateFloat($a);
            $b = EngineeringValidations::validateFloat($b);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) (abs($a - $b) < 1.0e-15);
    }

    /**
     * GESTEP.
     *
     *    Excel Function:
     *        GESTEP(number[,step])
     *
     *    Returns 1 if number >= step; returns 0 (zero) otherwise
     *    Use this function to filter a set of values. For example, by summing several GESTEP
     *        functions you calculate the count of values that exceed a threshold.
     *
     * @param array|float $number the value to test against step
     *                      Or can be an array of values
     * @param array|float $step The threshold value. If you omit a value for step, GESTEP uses zero.
     *                      Or can be an array of values
     *
     * @return array|int|string (string in the event of an error)
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function GESTEP($number, $step = 0.0)
    {
        if (is_array($number) || is_array($step)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $step);
        }

        try {
            $number = EngineeringValidations::validateFloat($number);
            $step = EngineeringValidations::validateFloat($step);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) ($number >= $step);
    }
}
