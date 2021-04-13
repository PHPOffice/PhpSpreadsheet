<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Gamma extends GammaBase
{
    /**
     * GAMMA.
     *
     * Return the gamma function value.
     *
     * @param mixed $value Float value for which we want the probability
     *
     * @return float|string The result, or a string containing an error
     */
    public static function gamma($value)
    {
        $value = Functions::flattenSingleValue($value);

        try {
            $value = DistributionValidations::validateFloat($value);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ((((int) $value) == ((float) $value)) && $value <= 0.0) {
            return Functions::NAN();
        }

        return self::gammaValue($value);
    }

    /**
     * GAMMADIST.
     *
     * Returns the gamma distribution.
     *
     * @param mixed $value Float Value at which you want to evaluate the distribution
     * @param mixed $a Parameter to the distribution as a float
     * @param mixed $b Parameter to the distribution as a float
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *
     * @return float|string
     */
    public static function distribution($value, $a, $b, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        try {
            $value = DistributionValidations::validateFloat($value);
            $a = DistributionValidations::validateFloat($a);
            $b = DistributionValidations::validateFloat($b);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($a <= 0) || ($b <= 0)) {
            return Functions::NAN();
        }

        return self::calculateDistribution($value, $a, $b, $cumulative);
    }

    /**
     * GAMMAINV.
     *
     * Returns the inverse of the Gamma distribution.
     *
     * @param mixed $probability Float probability at which you want to evaluate the distribution
     * @param mixed $alpha Parameter to the distribution as a float
     * @param mixed $beta Parameter to the distribution as a float
     *
     * @return float|string
     */
    public static function inverse($probability, $alpha, $beta)
    {
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);

        try {
            $probability = DistributionValidations::validateProbability($probability);
            $alpha = DistributionValidations::validateFloat($alpha);
            $beta = DistributionValidations::validateFloat($beta);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($alpha <= 0.0) || ($beta <= 0.0)) {
            return Functions::NAN();
        }

        return self::calculateInverse($probability, $alpha, $beta);
    }

    /**
     * GAMMALN.
     *
     * Returns the natural logarithm of the gamma function.
     *
     * @param mixed $value Float Value at which you want to evaluate the distribution
     *
     * @return float|string
     */
    public static function ln($value)
    {
        $value = Functions::flattenSingleValue($value);

        try {
            $value = DistributionValidations::validateFloat($value);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($value <= 0) {
            return Functions::NAN();
        }

        return log(self::gammaValue($value));
    }
}
