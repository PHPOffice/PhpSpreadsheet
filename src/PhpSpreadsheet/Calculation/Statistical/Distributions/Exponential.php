<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Exponential
{
    /**
     * EXPONDIST.
     *
     *    Returns the exponential distribution. Use EXPONDIST to model the time between events,
     *        such as how long an automated bank teller takes to deliver cash. For example, you can
     *        use EXPONDIST to determine the probability that the process takes at most 1 minute.
     *
     * @param mixed $value Float value for which we want the probability
     * @param mixed $lambda The parameter value as a float
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *
     * @return float|string
     */
    public static function distribution($value, $lambda, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $lambda = Functions::flattenSingleValue($lambda);
        $cumulative = Functions::flattenSingleValue($cumulative);

        try {
            $value = DistributionValidations::validateFloat($value);
            $lambda = DistributionValidations::validateFloat($lambda);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($lambda < 0)) {
            return Functions::NAN();
        }

        if ($cumulative === true) {
            return 1 - exp(0 - $value * $lambda);
        }

        return $lambda * exp(0 - $value * $lambda);
    }
}
