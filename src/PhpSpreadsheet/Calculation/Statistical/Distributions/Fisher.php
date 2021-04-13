<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Fisher
{
    /**
     * FISHER.
     *
     * Returns the Fisher transformation at x. This transformation produces a function that
     *        is normally distributed rather than skewed. Use this function to perform hypothesis
     *        testing on the correlation coefficient.
     *
     * @param mixed $value Float value for which we want the probability
     *
     * @return float|string
     */
    public static function distribution($value)
    {
        $value = Functions::flattenSingleValue($value);

        try {
            DistributionValidations::validateFloat($value);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value <= -1) || ($value >= 1)) {
            return Functions::NAN();
        }

        return 0.5 * log((1 + $value) / (1 - $value));
    }

    /**
     * FISHERINV.
     *
     * Returns the inverse of the Fisher transformation. Use this transformation when
     *        analyzing correlations between ranges or arrays of data. If y = FISHER(x), then
     *        FISHERINV(y) = x.
     *
     * @param mixed $probability Float probability at which you want to evaluate the distribution
     *
     * @return float|string
     */
    public static function inverse($probability)
    {
        $probability = Functions::flattenSingleValue($probability);

        try {
            DistributionValidations::validateFloat($probability);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (exp(2 * $probability) - 1) / (exp(2 * $probability) + 1);
    }
}
