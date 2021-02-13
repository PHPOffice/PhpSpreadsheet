<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Erf
{
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
     * @param float $lower lower bound for integrating ERF
     * @param float $upper upper bound for integrating ERF.
     *                                If omitted, ERF integrates between zero and lower_limit
     *
     * @return float|string
     */
    public static function ERF($lower, $upper = null)
    {
        $lower = Functions::flattenSingleValue($lower);
        $upper = Functions::flattenSingleValue($upper);

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
     * @param float $limit bound for integrating ERF
     *
     * @return float|string
     */
    public static function ERFPRECISE($limit)
    {
        $limit = Functions::flattenSingleValue($limit);

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
