<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class StudentT
{
    use BaseValidations;

    private const MAX_ITERATIONS = 256;

    /**
     * TDIST.
     *
     * Returns the probability of Student's T distribution.
     *
     * @param mixed (float) $value Value for the function
     * @param mixed (float) $degrees degrees of freedom
     * @param mixed (int) $tails number of tails (1 or 2)
     *
     * @return float|string The result, or a string containing an error
     */
    public static function distribution($value, $degrees, $tails)
    {
        $value = Functions::flattenSingleValue($value);
        $degrees = Functions::flattenSingleValue($degrees);
        $tails = Functions::flattenSingleValue($tails);

        try {
            $value = self::validateFloat($value);
            $degrees = self::validateInt($degrees);
            $tails = self::validateInt($tails);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($degrees < 1) || ($tails < 1) || ($tails > 2)) {
            return Functions::NAN();
        }

        return self::calculateDistribution($value, $degrees, $tails);
    }

    /**
     * TINV.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed (float) $probability Probability for the function
     * @param mixed (float) $degrees degrees of freedom
     *
     * @return float|string The result, or a string containing an error
     */
    public static function inverse($probability, $degrees)
    {
        $probability = Functions::flattenSingleValue($probability);
        $degrees = Functions::flattenSingleValue($degrees);

        try {
            $probability = self::validateFloat($probability);
            $degrees = self::validateInt($degrees);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($probability < 0.0 || $probability > 1.0 || $degrees <= 0) {
            return Functions::NAN();
        }

        $callback = function ($value) use ($degrees) {
            return self::distribution($value, $degrees, 2);
        };

        $newtonRaphson = new NewtonRaphson($callback);

        return $newtonRaphson->execute($probability);
    }

    /**
     * @return float|int
     */
    private static function calculateDistribution(float $value, int $degrees, int $tails)
    {
        //    tdist, which finds the probability that corresponds to a given value
        //    of t with k degrees of freedom. This algorithm is translated from a
        //    pascal function on p81 of "Statistical Computing in Pascal" by D
        //    Cooke, A H Craven & G M Clark (1985: Edward Arnold (Pubs.) Ltd:
        //    London). The above Pascal algorithm is itself a translation of the
        //    fortran algoritm "AS 3" by B E Cooper of the Atlas Computer
        //    Laboratory as reported in (among other places) "Applied Statistics
        //    Algorithms", editied by P Griffiths and I D Hill (1985; Ellis
        //    Horwood Ltd.; W. Sussex, England).
        $tterm = $degrees;
        $ttheta = atan2($value, sqrt($tterm));
        $tc = cos($ttheta);
        $ts = sin($ttheta);

        if (($degrees % 2) === 1) {
            $ti = 3;
            $tterm = $tc;
        } else {
            $ti = 2;
            $tterm = 1;
        }

        $tsum = $tterm;
        while ($ti < $degrees) {
            $tterm *= $tc * $tc * ($ti - 1) / $ti;
            $tsum += $tterm;
            $ti += 2;
        }

        $tsum *= $ts;
        if (($degrees % 2) == 1) {
            $tsum = Functions::M_2DIVPI * ($tsum + $ttheta);
        }

        $tValue = 0.5 * (1 + $tsum);
        if ($tails == 1) {
            return 1 - abs($tValue);
        }

        return 1 - abs((1 - $tValue) - $tValue);
    }
}
