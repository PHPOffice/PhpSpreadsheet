<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class StudentT
{
    use ArrayEnabled;

    /**
     * TDIST.
     *
     * Returns the probability of Student's T distribution.
     *
     * @param mixed $value Float value for the distribution
     *                      Or can be an array of values
     * @param mixed $degrees Integer value for degrees of freedom
     *                      Or can be an array of values
     * @param mixed $tails Integer value for the number of tails (1 or 2)
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distribution(mixed $value, mixed $degrees, mixed $tails)
    {
        if (is_array($value) || is_array($degrees) || is_array($tails)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $degrees, $tails);
        }

        try {
            $value = DistributionValidations::validateFloat($value);
            $degrees = DistributionValidations::validateInt($degrees);
            $tails = DistributionValidations::validateInt($tails);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($degrees < 1) || ($tails < 1) || ($tails > 2)) {
            return ExcelError::NAN();
        }

        return self::calculateDistribution($value, $degrees, $tails);
    }

    /**
     * TINV.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed $probability Float probability for the function
     *                      Or can be an array of values
     * @param mixed $degrees Integer value for degrees of freedom
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function inverse(mixed $probability, mixed $degrees)
    {
        if (is_array($probability) || is_array($degrees)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $probability, $degrees);
        }

        try {
            $probability = DistributionValidations::validateProbability($probability);
            $degrees = DistributionValidations::validateInt($degrees);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($degrees <= 0) {
            return ExcelError::NAN();
        }

        $callback = fn ($value) => self::distribution($value, $degrees, 2);

        $newtonRaphson = new NewtonRaphson($callback);

        return $newtonRaphson->execute($probability);
    }

    private static function calculateDistribution(float $value, int $degrees, int $tails): float
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
