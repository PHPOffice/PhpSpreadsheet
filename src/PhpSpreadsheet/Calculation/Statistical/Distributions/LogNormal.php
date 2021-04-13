<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class LogNormal
{
    /**
     * LOGNORMDIST.
     *
     * Returns the cumulative lognormal distribution of x, where ln(x) is normally distributed
     * with parameters mean and standard_dev.
     *
     * @param mixed $value Float value for which we want the probability
     * @param mixed $mean Mean value as a float
     * @param mixed $stdDev Standard Deviation as a float
     *
     * @return float|string The result, or a string containing an error
     */
    public static function cumulative($value, $mean, $stdDev)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        try {
            $value = DistributionValidations::validateFloat($value);
            $mean = DistributionValidations::validateFloat($mean);
            $stdDev = DistributionValidations::validateFloat($stdDev);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value <= 0) || ($stdDev <= 0)) {
            return Functions::NAN();
        }

        return StandardNormal::cumulative((log($value) - $mean) / $stdDev);
    }

    /**
     * LOGNORM.DIST.
     *
     * Returns the lognormal distribution of x, where ln(x) is normally distributed
     * with parameters mean and standard_dev.
     *
     * @param mixed $value Float value for which we want the probability
     * @param mixed $mean Mean value as a float
     * @param mixed $stdDev Standard Deviation as a float
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *
     * @return float|string The result, or a string containing an error
     */
    public static function distribution($value, $mean, $stdDev, $cumulative = false)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $cumulative = Functions::flattenSingleValue($cumulative);

        try {
            $value = DistributionValidations::validateFloat($value);
            $mean = DistributionValidations::validateFloat($mean);
            $stdDev = DistributionValidations::validateFloat($stdDev);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value <= 0) || ($stdDev <= 0)) {
            return Functions::NAN();
        }

        if ($cumulative === true) {
            return StandardNormal::distribution((log($value) - $mean) / $stdDev, true);
        }

        return (1 / (sqrt(2 * M_PI) * $stdDev * $value)) *
            exp(0 - ((log($value) - $mean) ** 2 / (2 * $stdDev ** 2)));
    }

    /**
     * LOGINV.
     *
     * Returns the inverse of the lognormal cumulative distribution
     *
     * @param mixed $probability Float probability for which we want the value
     * @param mixed $mean Mean Value as a float
     * @param mixed $stdDev Standard Deviation as a float
     *
     * @return float|string The result, or a string containing an error
     *
     * @TODO    Try implementing P J Acklam's refinement algorithm for greater
     *            accuracy if I can get my head round the mathematics
     *            (as described at) http://home.online.no/~pjacklam/notes/invnorm/
     */
    public static function inverse($probability, $mean, $stdDev)
    {
        $probability = Functions::flattenSingleValue($probability);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        try {
            $probability = DistributionValidations::validateProbability($probability);
            $mean = DistributionValidations::validateFloat($mean);
            $stdDev = DistributionValidations::validateFloat($stdDev);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($stdDev <= 0) {
            return Functions::NAN();
        }

        return exp($mean + $stdDev * StandardNormal::inverse($probability));
    }
}
