<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

class StandardNormal
{
    /**
     * NORMSDIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed $value Float value for which we want the probability
     *
     * @return float|string The result, or a string containing an error
     */
    public static function cumulative($value)
    {
        return Normal::distribution($value, 0, 1, true);
    }

    /**
     * NORM.S.DIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed $value Float value for which we want the probability
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *
     * @return float|string The result, or a string containing an error
     */
    public static function distribution($value, $cumulative)
    {
        return Normal::distribution($value, 0, 1, $cumulative);
    }

    /**
     * NORMSINV.
     *
     * Returns the inverse of the standard normal cumulative distribution
     *
     * @param mixed $value Float probability for which we want the value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function inverse($value)
    {
        return Normal::inverse($value, 0, 1);
    }

    /**
     * GAUSS.
     *
     * Calculates the probability that a member of a standard normal population will fall between
     *     the mean and z standard deviations from the mean.
     *
     * @param mixed $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function gauss($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }

        return self::distribution($value, true) - 0.5;
    }

    /**
     * ZTEST.
     *
     * Returns the one-tailed P-value of a z-test.
     *
     * For a given hypothesized population mean, x, Z.TEST returns the probability that the sample mean would be
     *     greater than the average of observations in the data set (array) — that is, the observed sample mean.
     *
     * @param mixed $dataSet The dataset should be an array of float values for the observations
     * @param mixed $m0 Alpha Parameter
     * @param mixed $sigma A null or float value for the Beta (Standard Deviation) Parameter;
     *                       if null, we use the standard deviation of the dataset
     *
     * @return float|string (string if result is an error)
     */
    public static function zTest($dataSet, $m0, $sigma = null)
    {
        $dataSet = Functions::flattenArrayIndexed($dataSet);
        $m0 = Functions::flattenSingleValue($m0);
        $sigma = Functions::flattenSingleValue($sigma);

        if (!is_numeric($m0) || ($sigma !== null && !is_numeric($sigma))) {
            return Functions::VALUE();
        }

        if ($sigma === null) {
            $sigma = StandardDeviations::STDEV($dataSet);
        }
        $n = count($dataSet);

        return 1 - self::cumulative((Averages::average($dataSet) - $m0) / ($sigma / sqrt($n)));
    }
}
