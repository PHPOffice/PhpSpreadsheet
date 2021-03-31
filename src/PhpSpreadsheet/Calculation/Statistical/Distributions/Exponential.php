<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Exponential
{
    use BaseValidations;

    /**
     * EXPONDIST.
     *
     *    Returns the exponential distribution. Use EXPONDIST to model the time between events,
     *        such as how long an automated bank teller takes to deliver cash. For example, you can
     *        use EXPONDIST to determine the probability that the process takes at most 1 minute.
     *
     * @param mixed (float) $value Value of the function
     * @param mixed (float) $lambda The parameter value
     * @param mixed (bool) $cumulative
     *
     * @return float|string
     */
    public static function distribution($value, $lambda, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $lambda = Functions::flattenSingleValue($lambda);
        $cumulative = Functions::flattenSingleValue($cumulative);

        try {
            $value = self::validateFloat($value);
            $lambda = self::validateFloat($lambda);
            $cumulative = self::validateBool($cumulative);
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
