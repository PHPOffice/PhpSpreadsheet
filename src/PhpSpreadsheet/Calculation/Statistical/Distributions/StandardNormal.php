<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

class StandardNormal
{
    use ArrayEnabled;

    /**
     * NORMSDIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * NOTE: We don't need to check for arrays to array-enable this function, because that is already
     *       handled by the logic in Normal::distribution()
     *       All we need to do is pass the value through as scalar or as array.
     *
     * @param mixed $value Float value for which we want the probability
     *                      Or can be an array of values
     *
     * @return array|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function cumulative(mixed $value)
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
     * NOTE: We don't need to check for arrays to array-enable this function, because that is already
     *       handled by the logic in Normal::distribution()
     *       All we need to do is pass the value and cumulative through as scalar or as array.
     *
     * @param mixed $value Float value for which we want the probability
     *                      Or can be an array of values
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *                      Or can be an array of values
     *
     * @return array|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distribution(mixed $value, mixed $cumulative)
    {
        return Normal::distribution($value, 0, 1, $cumulative);
    }

    /**
     * NORMSINV.
     *
     * Returns the inverse of the standard normal cumulative distribution
     *
     * @param mixed $value float probability for which we want the value
     *                      Or can be an array of values
     *
     * NOTE: We don't need to check for arrays to array-enable this function, because that is already
     *       handled by the logic in Normal::inverse()
     *       All we need to do is pass the value through as scalar or as array
     *
     * @return array|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function inverse(mixed $value)
    {
        return Normal::inverse($value, 0, 1);
    }

    /**
     * GAUSS.
     *
     * Calculates the probability that a member of a standard normal population will fall between
     *     the mean and z standard deviations from the mean.
     *
     * @param mixed $value Or can be an array of values
     *
     * @return array|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function gauss(mixed $value): array|string|float
    {
        if (is_array($value)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $value);
        }

        if (!is_numeric($value)) {
            return ExcelError::VALUE();
        }
        /** @var float $dist */
        $dist = self::distribution($value, true);

        return $dist - 0.5;
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
     *                      Or can be an array of values
     * @param mixed $sigma A null or float value for the Beta (Standard Deviation) Parameter;
     *                       if null, we use the standard deviation of the dataset
     *                      Or can be an array of values
     *
     * @return array|float|string (string if result is an error)
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function zTest(mixed $dataSet, mixed $m0, mixed $sigma = null)
    {
        if (is_array($m0) || is_array($sigma)) {
            return self::evaluateArrayArgumentsSubsetFrom([self::class, __FUNCTION__], 1, $dataSet, $m0, $sigma);
        }

        $dataSet = Functions::flattenArrayIndexed($dataSet);

        if (!is_numeric($m0) || ($sigma !== null && !is_numeric($sigma))) {
            return ExcelError::VALUE();
        }

        if ($sigma === null) {
            /** @var float $sigma */
            $sigma = StandardDeviations::STDEV($dataSet);
        }
        $n = count($dataSet);

        $sub1 = Averages::average($dataSet);

        if (!is_numeric($sub1)) {
            return $sub1;
        }

        $temp = self::cumulative(($sub1 - $m0) / ($sigma / sqrt($n)));

        return 1 - (is_numeric($temp) ? $temp : 0);
    }
}
