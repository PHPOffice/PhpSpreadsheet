<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Poisson
{
    use ArrayEnabled;

    /**
     * POISSON.
     *
     * Returns the Poisson distribution. A common application of the Poisson distribution
     * is predicting the number of events over a specific time, such as the number of
     * cars arriving at a toll plaza in 1 minute.
     *
     * @param mixed $value Float value for which we want the probability
     *                      Or can be an array of values
     * @param mixed $mean Mean value as a float
     *                      Or can be an array of values
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distribution(mixed $value, mixed $mean, mixed $cumulative): array|string|float
    {
        if (is_array($value) || is_array($mean) || is_array($cumulative)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $mean, $cumulative);
        }

        try {
            $value = DistributionValidations::validateFloat($value);
            $mean = DistributionValidations::validateFloat($mean);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($mean < 0)) {
            return ExcelError::NAN();
        }

        if ($cumulative) {
            $summer = 0;
            $floor = floor($value);
            for ($i = 0; $i <= $floor; ++$i) {
                /** @var float $fact */
                $fact = MathTrig\Factorial::fact($i);
                $summer += $mean ** $i / $fact;
            }

            return exp(0 - $mean) * $summer;
        }
        /** @var float $fact */
        $fact = MathTrig\Factorial::fact($value);

        return (exp(0 - $mean) * $mean ** $value) / $fact;
    }
}
