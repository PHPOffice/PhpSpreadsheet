<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Logarithms
{
    /**
     * LOG_BASE.
     *
     * Returns the logarithm of a number to a specified base. The default base is 10.
     *
     * Excel Function:
     *        LOG(number[,base])
     *
     * @param mixed $number The positive real number for which you want the logarithm
     * @param mixed $base The base of the logarithm. If base is omitted, it is assumed to be 10.
     *
     * @return float|string The result, or a string containing an error
     */
    public static function withBase($number, $base = 10)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            Helpers::validatePositive($number);
            $base = Helpers::validateNumericNullBool($base);
            Helpers::validatePositive($base);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return log($number, $base);
    }

    /**
     * LOG10.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function base10($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            Helpers::validatePositive($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return log10($number);
    }

    /**
     * LN.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function natural($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
            Helpers::validatePositive($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return log($number);
    }
}
