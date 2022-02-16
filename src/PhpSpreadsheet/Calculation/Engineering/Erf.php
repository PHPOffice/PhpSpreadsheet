<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Erf
{
    use ArrayEnabled;

    private static $twoSqrtPi = 1.128379167095512574;

    /**
     * ERF.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the upper or lower bound arguments,
     *            the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *            improved, so that it can now calculate the function for both positive and negative ranges.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts negative arguments.
     *
     *    Excel Function:
     *        ERF(lower[,upper])
     *
     * @param mixed $lower Lower bound float for integrating ERF
     *                      Or can be an array of values
     * @param mixed $upper Upper bound float for integrating ERF.
     *                           If omitted, ERF integrates between zero and lower_limit
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function ERF($lower, $upper = null)
    {
        if (is_array($lower) || is_array($upper)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $lower, $upper);
        }

        if (is_numeric($lower)) {
            if ($upper === null) {
                return self::erfValue($lower);
            }
            if (is_numeric($upper)) {
                return self::erfValue($upper) - self::erfValue($lower);
            }
        }

        return Functions::VALUE();
    }

    /**
     * ERFPRECISE.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Excel Function:
     *        ERF.PRECISE(limit)
     *
     * @param mixed $limit Float bound for integrating ERF, other bound is zero
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function ERFPRECISE($limit)
    {
        if (is_array($limit)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $limit);
        }

        return self::ERF($limit);
    }

    //
    //    Private method to calculate the erf value
    //
    public static function erfValue($value)
    {
        if (abs($value) > 2.2) {
            return 1 - ErfC::ERFC($value);
        }
        $sum = $term = $value;
        $xsqr = ($value * $value);
        $j = 1;
        do {
            $term *= $xsqr / $j;
            $sum -= $term / (2 * $j + 1);
            ++$j;
            $term *= $xsqr / $j;
            $sum += $term / (2 * $j + 1);
            ++$j;
            if ($sum == 0.0) {
                break;
            }
        } while (abs($term / $sum) > Functions::PRECISION);

        return self::$twoSqrtPi * $sum;
    }
}
