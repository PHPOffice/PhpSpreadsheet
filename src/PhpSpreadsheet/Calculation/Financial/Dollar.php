<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

class Dollar
{
    use ArrayEnabled;

    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @param mixed $number The value to format, or can be an array of numbers
     *                         Or can be an array of values
     * @param mixed $precision The number of digits to display to the right of the decimal point (as an integer).
     *                            If precision is negative, number is rounded to the left of the decimal point.
     *                            If you omit precision, it is assumed to be 2
     *              Or can be an array of precision values
     *
     * @return array|string
     *         If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function format(mixed $number, mixed $precision = 2)
    {
        return Format::DOLLAR($number, $precision);
    }

    /**
     * DOLLARDE.
     *
     * Converts a dollar price expressed as an integer part and a fraction
     *        part into a dollar price expressed as a decimal number.
     * Fractional dollar numbers are sometimes used for security prices.
     *
     * Excel Function:
     *        DOLLARDE(fractional_dollar,fraction)
     *
     * @param mixed $fractionalDollar Fractional Dollar
     *              Or can be an array of values
     * @param mixed $fraction Fraction
     *              Or can be an array of values
     */
    public static function decimal(mixed $fractionalDollar = null, mixed $fraction = 0): array|string|float
    {
        if (is_array($fractionalDollar) || is_array($fraction)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $fractionalDollar, $fraction);
        }

        try {
            $fractionalDollar = FinancialValidations::validateFloat(
                Functions::flattenSingleValue($fractionalDollar) ?? 0.0
            );
            $fraction = FinancialValidations::validateInt(Functions::flattenSingleValue($fraction));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Additional parameter validations
        if ($fraction < 0) {
            return ExcelError::NAN();
        }
        if ($fraction == 0) {
            return ExcelError::DIV0();
        }

        $dollars = ($fractionalDollar < 0) ? ceil($fractionalDollar) : floor($fractionalDollar);
        $cents = fmod($fractionalDollar, 1.0);
        $cents /= $fraction;
        $cents *= 10 ** ceil(log10($fraction));

        return $dollars + $cents;
    }

    /**
     * DOLLARFR.
     *
     * Converts a dollar price expressed as a decimal number into a dollar price
     *        expressed as a fraction.
     * Fractional dollar numbers are sometimes used for security prices.
     *
     * Excel Function:
     *        DOLLARFR(decimal_dollar,fraction)
     *
     * @param mixed $decimalDollar Decimal Dollar
     *              Or can be an array of values
     * @param mixed $fraction Fraction
     *              Or can be an array of values
     */
    public static function fractional(mixed $decimalDollar = null, mixed $fraction = 0): array|string|float
    {
        if (is_array($decimalDollar) || is_array($fraction)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $decimalDollar, $fraction);
        }

        try {
            $decimalDollar = FinancialValidations::validateFloat(
                Functions::flattenSingleValue($decimalDollar) ?? 0.0
            );
            $fraction = FinancialValidations::validateInt(Functions::flattenSingleValue($fraction));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Additional parameter validations
        if ($fraction < 0) {
            return ExcelError::NAN();
        }
        if ($fraction == 0) {
            return ExcelError::DIV0();
        }

        $dollars = ($decimalDollar < 0.0) ? ceil($decimalDollar) : floor($decimalDollar);
        $cents = fmod($decimalDollar, 1);
        $cents *= $fraction;
        $cents *= 10 ** (-ceil(log10($fraction)));

        return $dollars + $cents;
    }
}
