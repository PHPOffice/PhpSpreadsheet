<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Poisson
{
    use BaseValidations;

    /**
     * POISSON.
     *
     * Returns the Poisson distribution. A common application of the Poisson distribution
     * is predicting the number of events over a specific time, such as the number of
     * cars arriving at a toll plaza in 1 minute.
     *
     * @param mixed (float) $value
     * @param mixed (float) $mean Mean Value
     * @param mixed (bool) $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function distribution($value, $mean, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);

        try {
            $value = self::validateFloat($value);
            $mean = self::validateFloat($mean);
            $cumulative = self::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($mean < 0)) {
            return Functions::NAN();
        }

        if ($cumulative) {
            $summer = 0;
            $floor = floor($value);
            for ($i = 0; $i <= $floor; ++$i) {
                $summer += $mean ** $i / MathTrig\Fact::funcFact($i);
            }

            return exp(0 - $mean) * $summer;
        }

        return (exp(0 - $mean) * $mean ** $value) / MathTrig\Fact::funcFact($value);
    }
}
