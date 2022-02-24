<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Random
{
    use ArrayEnabled;

    /**
     * RAND.
     *
     * @return float Random number
     */
    public static function rand()
    {
        return (mt_rand(0, 10000000)) / 10000000;
    }

    /**
     * RANDBETWEEN.
     *
     * @param mixed $min Minimal value
     *                      Or can be an array of values
     * @param mixed $max Maximal value
     *                      Or can be an array of values
     *
     * @return array|float|int|string Random number
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function randBetween($min, $max)
    {
        if (is_array($min) || is_array($max)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $min, $max);
        }

        try {
            $min = (int) Helpers::validateNumericNullBool($min);
            $max = (int) Helpers::validateNumericNullBool($max);
            Helpers::validateNotNegative($max - $min);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return mt_rand($min, $max);
    }

    /**
     * RANDARRAY.
     *
     * Generates a list of sequential numbers in an array.
     *
     * Excel Function:
     *      RANDARRAY([rows],[columns],[start],[step])
     *
     * @param mixed $rows the number of rows to return, defaults to 1
     * @param mixed $columns the number of columns to return, defaults to 1
     * @param mixed $min the minimum number to be returned, defaults to 0
     * @param mixed $max the maximum number to be returned, defaults to 1
     * @param bool $wholeNumber the type of numbers to return:
     *                             False - Decimal numbers to 15 decimal places. (default)
     *                             True - Whole (integer) numbers
     *
     * @return array|string The resulting array, or a string containing an error
     */
    public static function randArray($rows = 1, $columns = 1, $min = 0, $max = 1, $wholeNumber = false)
    {
        try {
            $rows = (int) Helpers::validateNumericNullSubstitution($rows, 1);
            Helpers::validatePositive($rows);
            $columns = (int) Helpers::validateNumericNullSubstitution($columns, 1);
            Helpers::validatePositive($columns);
            $min = Helpers::validateNumericNullSubstitution($min, 1);
            $max = Helpers::validateNumericNullSubstitution($max, 1);

            if ($max <= $min) {
                return ExcelError::VALUE();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return array_chunk(
            array_map(
                function () use ($min, $max, $wholeNumber) {
                    return $wholeNumber
                        ? mt_rand((int) $min, (int) $max)
                        : (mt_rand() / mt_getrandmax()) * ($max - $min) + $min;
                },
                array_fill(0, $rows * $columns, $min)
            ),
            max($columns, 1)
        );
    }
}
