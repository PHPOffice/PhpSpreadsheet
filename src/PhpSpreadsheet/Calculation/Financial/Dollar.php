<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

class Dollar
{
    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @param mixed $number The value to format
     * @param mixed $precision The number of digits to display to the right of the decimal point (as an integer).
     *                            If precision is negative, number is rounded to the left of the decimal point.
     *                            If you omit precision, it is assumed to be 2
     */
    public static function format($number, $precision = 2): string
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
     * @param mixed $fraction Fraction
     *
     * @return float|string
     */
    public static function decimal($fractionalDollar = null, $fraction = 0)
    {
        $fractionalDollar = Functions::flattenSingleValue($fractionalDollar);
        $fraction = (int) Functions::flattenSingleValue($fraction);

        // Validate parameters
        if ($fractionalDollar === null || $fraction < 0) {
            return Functions::NAN();
        }
        if ($fraction == 0) {
            return Functions::DIV0();
        }

        $dollars = floor($fractionalDollar);
        $cents = fmod($fractionalDollar, 1);
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
     * @param mixed $fraction Fraction
     *
     * @return float|string
     */
    public static function fractional($decimalDollar = null, $fraction = 0)
    {
        $decimalDollar = Functions::flattenSingleValue($decimalDollar);
        $fraction = (int) Functions::flattenSingleValue($fraction);

        // Validate parameters
        if ($decimalDollar === null || $fraction < 0) {
            return Functions::NAN();
        }
        if ($fraction == 0) {
            return Functions::DIV0();
        }

        $dollars = floor($decimalDollar);
        $cents = fmod($decimalDollar, 1);
        $cents *= $fraction;
        $cents *= 10 ** (-ceil(log10($fraction)));

        return $dollars + $cents;
    }
}
