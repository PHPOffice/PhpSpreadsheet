<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Weibull
{
    use BaseValidations;

    /**
     * WEIBULL.
     *
     * Returns the Weibull distribution. Use this distribution in reliability
     * analysis, such as calculating a device's mean time to failure.
     *
     * @param mixed (float) $value
     * @param mixed (float) $alpha Alpha Parameter
     * @param mixed (float) $beta Beta Parameter
     * @param mixed (bool) $cumulative
     *
     * @return float|string (string if result is an error)
     */
    public static function distribution($value, $alpha, $beta, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);
        $cumulative = Functions::flattenSingleValue($cumulative);

        try {
            $value = self::validateFloat($value);
            $alpha = self::validateFloat($alpha);
            $beta = self::validateFloat($beta);
            $cumulative = self::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($alpha <= 0) || ($beta <= 0)) {
            return Functions::NAN();
        }

        if ($cumulative) {
            return 1 - exp(0 - ($value / $beta) ** $alpha);
        }

        return ($alpha / $beta ** $alpha) * $value ** ($alpha - 1) * exp(0 - ($value / $beta) ** $alpha);
    }
}
