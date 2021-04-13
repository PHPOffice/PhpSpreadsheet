<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Compare
{
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
     * @param float $a the first number
     * @param float $b The second number. If omitted, b is assumed to be zero.
     *
     * @return int|string (string in the event of an error)
     */
    public static function DELTA($a, $b = 0)
    {
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        try {
            $a = EngineeringValidations::validateFloat($a);
            $b = EngineeringValidations::validateFloat($b);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) ($a == $b);
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
     * @param float $number the value to test against step
     * @param float $step The threshold value. If you omit a value for step, GESTEP uses zero.
     *
     * @return int|string (string in the event of an error)
     */
    public static function GESTEP($number, $step = 0)
    {
        $number = Functions::flattenSingleValue($number);
        $step = Functions::flattenSingleValue($step);

        try {
            $number = EngineeringValidations::validateFloat($number);
            $step = EngineeringValidations::validateFloat($step);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) ($number >= $step);
    }
}
