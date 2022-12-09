<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class BitWise
{
    use ArrayEnabled;

    const SPLIT_DIVISOR = 2 ** 24;

    /**
     * Split a number into upper and lower portions for full 32-bit support.
     *
     * @param float|int $number
     *
     * @return int[]
     */
    private static function splitNumber($number): array
    {
        return [(int) floor($number / self::SPLIT_DIVISOR), (int) fmod($number, self::SPLIT_DIVISOR)];
    }

    /**
     * BITAND.
     *
     * Returns the bitwise AND of two integer values.
     *
     * Excel Function:
     *        BITAND(number1, number2)
     *
     * @param array|int $number1
     *                      Or can be an array of values
     * @param array|int $number2
     *                      Or can be an array of values
     *
     * @return array|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BITAND($number1, $number2)
    {
        if (is_array($number1) || is_array($number2)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number1, $number2);
        }

        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $split1 = self::splitNumber($number1);
        $split2 = self::splitNumber($number2);

        return  self::SPLIT_DIVISOR * ($split1[0] & $split2[0]) + ($split1[1] & $split2[1]);
    }

    /**
     * BITOR.
     *
     * Returns the bitwise OR of two integer values.
     *
     * Excel Function:
     *        BITOR(number1, number2)
     *
     * @param array|int $number1
     *                      Or can be an array of values
     * @param array|int $number2
     *                      Or can be an array of values
     *
     * @return array|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BITOR($number1, $number2)
    {
        if (is_array($number1) || is_array($number2)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number1, $number2);
        }

        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $split1 = self::splitNumber($number1);
        $split2 = self::splitNumber($number2);

        return  self::SPLIT_DIVISOR * ($split1[0] | $split2[0]) + ($split1[1] | $split2[1]);
    }

    /**
     * BITXOR.
     *
     * Returns the bitwise XOR of two integer values.
     *
     * Excel Function:
     *        BITXOR(number1, number2)
     *
     * @param array|int $number1
     *                      Or can be an array of values
     * @param array|int $number2
     *                      Or can be an array of values
     *
     * @return array|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BITXOR($number1, $number2)
    {
        if (is_array($number1) || is_array($number2)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number1, $number2);
        }

        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $split1 = self::splitNumber($number1);
        $split2 = self::splitNumber($number2);

        return  self::SPLIT_DIVISOR * ($split1[0] ^ $split2[0]) + ($split1[1] ^ $split2[1]);
    }

    /**
     * BITLSHIFT.
     *
     * Returns the number value shifted left by shift_amount bits.
     *
     * Excel Function:
     *        BITLSHIFT(number, shift_amount)
     *
     * @param array|int $number
     *                      Or can be an array of values
     * @param array|int $shiftAmount
     *                      Or can be an array of values
     *
     * @return array|float|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BITLSHIFT($number, $shiftAmount)
    {
        if (is_array($number) || is_array($shiftAmount)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $shiftAmount);
        }

        try {
            $number = self::validateBitwiseArgument($number);
            $shiftAmount = self::validateShiftAmount($shiftAmount);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $result = floor($number * (2 ** $shiftAmount));
        if ($result > 2 ** 48 - 1) {
            return ExcelError::NAN();
        }

        return $result;
    }

    /**
     * BITRSHIFT.
     *
     * Returns the number value shifted right by shift_amount bits.
     *
     * Excel Function:
     *        BITRSHIFT(number, shift_amount)
     *
     * @param array|int $number
     *                      Or can be an array of values
     * @param array|int $shiftAmount
     *                      Or can be an array of values
     *
     * @return array|float|int|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BITRSHIFT($number, $shiftAmount)
    {
        if (is_array($number) || is_array($shiftAmount)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $number, $shiftAmount);
        }

        try {
            $number = self::validateBitwiseArgument($number);
            $shiftAmount = self::validateShiftAmount($shiftAmount);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $result = floor($number / (2 ** $shiftAmount));
        if ($result > 2 ** 48 - 1) { // possible because shiftAmount can be negative
            return ExcelError::NAN();
        }

        return $result;
    }

    /**
     * Validate arguments passed to the bitwise functions.
     *
     * @param mixed $value
     *
     * @return float
     */
    private static function validateBitwiseArgument($value)
    {
        $value = self::nullFalseTrueToNumber($value);

        if (is_numeric($value)) {
            $value = (float) $value;
            if ($value == floor($value)) {
                if (($value > 2 ** 48 - 1) || ($value < 0)) {
                    throw new Exception(ExcelError::NAN());
                }

                return floor($value);
            }

            throw new Exception(ExcelError::NAN());
        }

        throw new Exception(ExcelError::VALUE());
    }

    /**
     * Validate arguments passed to the bitwise functions.
     *
     * @param mixed $value
     *
     * @return int
     */
    private static function validateShiftAmount($value)
    {
        $value = self::nullFalseTrueToNumber($value);

        if (is_numeric($value)) {
            if (abs($value) > 53) {
                throw new Exception(ExcelError::NAN());
            }

            return (int) $value;
        }

        throw new Exception(ExcelError::VALUE());
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @param mixed $number
     *
     * @return mixed
     */
    private static function nullFalseTrueToNumber(&$number)
    {
        if ($number === null) {
            $number = 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }

        return $number;
    }
}
