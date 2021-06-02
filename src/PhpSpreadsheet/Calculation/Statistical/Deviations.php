<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Deviations
{
    /**
     * DEVSQ.
     *
     * Returns the sum of squares of deviations of data points from their sample mean.
     *
     * Excel Function:
     *        DEVSQ(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function sumSquares(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $aMean = Averages::average($aArgs);
        if (!is_numeric($aMean)) {
            return Functions::NAN();
        }

        // Return value
        $returnValue = 0.0;
        $aCount = -1;
        foreach ($aArgs as $k => $arg) {
            // Is it a numeric value?
            if (
                (is_bool($arg)) &&
                ((!Functions::isCellValue($k)) ||
                    (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
            ) {
                $arg = (int) $arg;
            }
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ($arg - $aMean) ** 2;
                ++$aCount;
            }
        }

        return $aCount === 0 ? Functions::VALUE() : $returnValue;
    }

    /**
     * KURT.
     *
     * Returns the kurtosis of a data set. Kurtosis characterizes the relative peakedness
     * or flatness of a distribution compared with the normal distribution. Positive
     * kurtosis indicates a relatively peaked distribution. Negative kurtosis indicates a
     * relatively flat distribution.
     *
     * @param array ...$args Data Series
     *
     * @return float|string
     */
    public static function kurtosis(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = Averages::average($aArgs);
        if (!is_numeric($mean)) {
            return Functions::DIV0();
        }
        $stdDev = StandardDeviations::STDEV($aArgs);

        if ($stdDev > 0) {
            $count = $summer = 0;

            foreach ($aArgs as $k => $arg) {
                if ((is_bool($arg)) && (!Functions::isMatrixValue($k))) {
                } else {
                    // Is it a numeric value?
                    if ((is_numeric($arg)) && (!is_string($arg))) {
                        $summer += (($arg - $mean) / $stdDev) ** 4;
                        ++$count;
                    }
                }
            }

            if ($count > 3) {
                return $summer * ($count * ($count + 1) /
                        (($count - 1) * ($count - 2) * ($count - 3))) - (3 * ($count - 1) ** 2 /
                        (($count - 2) * ($count - 3)));
            }
        }

        return Functions::DIV0();
    }

    /**
     * SKEW.
     *
     * Returns the skewness of a distribution. Skewness characterizes the degree of asymmetry
     * of a distribution around its mean. Positive skewness indicates a distribution with an
     * asymmetric tail extending toward more positive values. Negative skewness indicates a
     * distribution with an asymmetric tail extending toward more negative values.
     *
     * @param array ...$args Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function skew(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = Averages::average($aArgs);
        if (!is_numeric($mean)) {
            return Functions::DIV0();
        }
        $stdDev = StandardDeviations::STDEV($aArgs);
        if ($stdDev === 0.0 || is_string($stdDev)) {
            return Functions::DIV0();
        }

        $count = $summer = 0;
        // Loop through arguments
        foreach ($aArgs as $k => $arg) {
            if ((is_bool($arg)) && (!Functions::isMatrixValue($k))) {
            } elseif (!is_numeric($arg)) {
                return Functions::VALUE();
            } else {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $summer += (($arg - $mean) / $stdDev) ** 3;
                    ++$count;
                }
            }
        }

        if ($count > 2) {
            return $summer * ($count / (($count - 1) * ($count - 2)));
        }

        return Functions::DIV0();
    }
}
