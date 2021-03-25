<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Gamma extends GammaBase
{
    use BaseValidations;

    /**
     * GAMMA.
     *
     * Return the gamma function value.
     *
     * @param mixed (float) $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function gamma($value)
    {
        $value = Functions::flattenSingleValue($value);

        try {
            $value = self::validateFloat($value);
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
     * @param mixed (float) $value Value at which you want to evaluate the distribution
     * @param mixed (float) $a Parameter to the distribution
     * @param mixed (float) $b Parameter to the distribution
     * @param mixed (bool) $cumulative
     *
     * @return float|string
     */
    public static function distribution($value, $a, $b, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        try {
            $value = self::validateFloat($value);
            $a = self::validateFloat($a);
            $b = self::validateFloat($b);
            $cumulative = self::validateBool($cumulative);
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
     * @param mixed (float) $probability Probability at which you want to evaluate the distribution
     * @param mixed (float) $alpha Parameter to the distribution
     * @param mixed (float) $beta Parameter to the distribution
     *
     * @return float|string
     */
    public static function inverse($probability, $alpha, $beta)
    {
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);

        try {
            $probability = self::validateFloat($probability);
            $alpha = self::validateFloat($alpha);
            $beta = self::validateFloat($beta);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($alpha <= 0.0) || ($beta <= 0.0) || ($probability < 0.0) || ($probability > 1.0)) {
            return Functions::NAN();
        }

        return self::calculateInverse($probability, $alpha, $beta);
    }

    /**
     * GAMMALN.
     *
     * Returns the natural logarithm of the gamma function.
     *
     * @param mixed (float) $value
     *
     * @return float|string
     */
    public static function ln($value)
    {
        $value = Functions::flattenSingleValue($value);

        try {
            $value = self::validateFloat($value);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($value <= 0) {
            return Functions::NAN();
        }

        return log(self::gammaValue($value));
    }
}
