<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class BitWise
{
    /**
     * BITAND.
     *
     * Returns the bitwise AND of two integer values.
     *
     * Excel Function:
     *        BITAND(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITAND($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 & $number2;
    }

    /**
     * BITOR.
     *
     * Returns the bitwise OR of two integer values.
     *
     * Excel Function:
     *        BITOR(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 | $number2;
    }

    /**
     * BITXOR.
     *
     * Returns the bitwise XOR of two integer values.
     *
     * Excel Function:
     *        BITXOR(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITXOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 ^ $number2;
    }

    /**
     * BITLSHIFT.
     *
     * Returns the number value shifted left by shift_amount bits.
     *
     * Excel Function:
     *        BITLSHIFT(number, shift_amount)
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITLSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        $result = $number << $shiftAmount;
        if ($result > 2 ** 48 - 1) {
            return Functions::NAN();
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
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITRSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        return $number >> $shiftAmount;
    }

    /**
     * Validate arguments passed to the bitwise functions.
     *
     * @param mixed $value
     *
     * @return int
     */
    private static function validateBitwiseArgument($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_int($value)) {
            return $value;
        } elseif (is_numeric($value)) {
            if ($value == (int) ($value)) {
                $value = (int) ($value);
                if (($value > 2 ** 48 - 1) || ($value < 0)) {
                    throw new Exception(Functions::NAN());
                }

                return $value;
            }

            throw new Exception(Functions::NAN());
        }

        throw new Exception(Functions::VALUE());
    }
}
