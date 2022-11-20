<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class ChiSquared
{
    use ArrayEnabled;

    private const MAX_ITERATIONS = 256;

    private const EPS = 2.22e-16;

    /**
     * CHIDIST.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed $value Float value for which we want the probability
     *                      Or can be an array of values
     * @param mixed $degrees Integer degrees of freedom
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distributionRightTail($value, $degrees)
    {
        if (is_array($value) || is_array($degrees)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $degrees);
        }

        try {
            $value = DistributionValidations::validateFloat($value);
            $degrees = DistributionValidations::validateInt($degrees);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($degrees < 1) {
            return ExcelError::NAN();
        }
        if ($value < 0) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return 1;
            }

            return ExcelError::NAN();
        }

        return 1 - (Gamma::incompleteGamma($degrees / 2, $value / 2) / Gamma::gammaValue($degrees / 2));
    }

    /**
     * CHIDIST.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed $value Float value for which we want the probability
     *                      Or can be an array of values
     * @param mixed $degrees Integer degrees of freedom
     *                      Or can be an array of values
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distributionLeftTail($value, $degrees, $cumulative)
    {
        if (is_array($value) || is_array($degrees) || is_array($cumulative)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $degrees, $cumulative);
        }

        try {
            $value = DistributionValidations::validateFloat($value);
            $degrees = DistributionValidations::validateInt($degrees);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($degrees < 1) {
            return ExcelError::NAN();
        }
        if ($value < 0) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return 1;
            }

            return ExcelError::NAN();
        }

        if ($cumulative === true) {
            return 1 - self::distributionRightTail($value, $degrees);
        }

        return ($value ** (($degrees / 2) - 1) * exp(-$value / 2)) /
            ((2 ** ($degrees / 2)) * Gamma::gammaValue($degrees / 2));
    }

    /**
     * CHIINV.
     *
     * Returns the inverse of the right-tailed probability of the chi-squared distribution.
     *
     * @param mixed $probability Float probability at which you want to evaluate the distribution
     *                      Or can be an array of values
     * @param mixed $degrees Integer degrees of freedom
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function inverseRightTail($probability, $degrees)
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

        if ($degrees < 1) {
            return ExcelError::NAN();
        }

        $callback = function ($value) use ($degrees) {
            return 1 - (Gamma::incompleteGamma($degrees / 2, $value / 2)
                    / Gamma::gammaValue($degrees / 2));
        };

        $newtonRaphson = new NewtonRaphson($callback);

        return $newtonRaphson->execute($probability);
    }

    /**
     * CHIINV.
     *
     * Returns the inverse of the left-tailed probability of the chi-squared distribution.
     *
     * @param mixed $probability Float probability at which you want to evaluate the distribution
     *                      Or can be an array of values
     * @param mixed $degrees Integer degrees of freedom
     *                      Or can be an array of values
     *
     * @return array|float|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function inverseLeftTail($probability, $degrees)
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

        if ($degrees < 1) {
            return ExcelError::NAN();
        }

        return self::inverseLeftTailCalculation($probability, $degrees);
    }

    /**
     * CHITEST.
     *
     * Uses the chi-square test to calculate the probability that the differences between two supplied data sets
     *      (of observed and expected frequencies), are likely to be simply due to sampling error,
     *      or if they are likely to be real.
     *
     * @param mixed $actual an array of observed frequencies
     * @param mixed $expected an array of expected frequencies
     *
     * @return float|string
     */
    public static function test($actual, $expected)
    {
        $rows = count($actual);
        $actual = Functions::flattenArray($actual);
        $expected = Functions::flattenArray($expected);
        $columns = count($actual) / $rows;

        $countActuals = count($actual);
        $countExpected = count($expected);
        if ($countActuals !== $countExpected || $countActuals === 1) {
            return ExcelError::NAN();
        }

        $result = 0.0;
        for ($i = 0; $i < $countActuals; ++$i) {
            if ($expected[$i] == 0.0) {
                return ExcelError::DIV0();
            } elseif ($expected[$i] < 0.0) {
                return ExcelError::NAN();
            }
            $result += (($actual[$i] - $expected[$i]) ** 2) / $expected[$i];
        }

        $degrees = self::degrees($rows, $columns);

        $result = Functions::scalar(self::distributionRightTail($result, $degrees));

        return $result;
    }

    protected static function degrees(int $rows, int $columns): int
    {
        if ($rows === 1) {
            return $columns - 1;
        } elseif ($columns === 1) {
            return $rows - 1;
        }

        return ($columns - 1) * ($rows - 1);
    }

    private static function inverseLeftTailCalculation(float $probability, int $degrees): float
    {
        // bracket the root
        $min = 0;
        $sd = sqrt(2.0 * $degrees);
        $max = 2 * $sd;
        $s = -1;

        while ($s * self::pchisq($max, $degrees) > $probability * $s) {
            $min = $max;
            $max += 2 * $sd;
        }

        // Find root using bisection
        $chi2 = 0.5 * ($min + $max);

        while (($max - $min) > self::EPS * $chi2) {
            if ($s * self::pchisq($chi2, $degrees) > $probability * $s) {
                $min = $chi2;
            } else {
                $max = $chi2;
            }
            $chi2 = 0.5 * ($min + $max);
        }

        return $chi2;
    }

    private static function pchisq($chi2, $degrees)
    {
        return self::gammp($degrees, 0.5 * $chi2);
    }

    private static function gammp($n, $x)
    {
        if ($x < 0.5 * $n + 1) {
            return self::gser($n, $x);
        }

        return 1 - self::gcf($n, $x);
    }

    // Return the incomplete gamma function P(n/2,x) evaluated by
    // series representation. Algorithm from numerical recipe.
    // Assume that n is a positive integer and x>0, won't check arguments.
    // Relative error controlled by the eps parameter
    private static function gser($n, $x)
    {
        /** @var float */
        $gln = Gamma::ln($n / 2);
        $a = 0.5 * $n;
        $ap = $a;
        $sum = 1.0 / $a;
        $del = $sum;
        for ($i = 1; $i < 101; ++$i) {
            ++$ap;
            $del = $del * $x / $ap;
            $sum += $del;
            if ($del < $sum * self::EPS) {
                break;
            }
        }

        return $sum * exp(-$x + $a * log($x) - $gln);
    }

    // Return the incomplete gamma function Q(n/2,x) evaluated by
    // its continued fraction representation. Algorithm from numerical recipe.
    // Assume that n is a postive integer and x>0, won't check arguments.
    // Relative error controlled by the eps parameter
    private static function gcf($n, $x)
    {
        /** @var float */
        $gln = Gamma::ln($n / 2);
        $a = 0.5 * $n;
        $b = $x + 1 - $a;
        $fpmin = 1.e-300;
        $c = 1 / $fpmin;
        $d = 1 / $b;
        $h = $d;
        for ($i = 1; $i < 101; ++$i) {
            $an = -$i * ($i - $a);
            $b += 2;
            $d = $an * $d + $b;
            if (abs($d) < $fpmin) {
                $d = $fpmin;
            }
            $c = $b + $an / $c;
            if (abs($c) < $fpmin) {
                $c = $fpmin;
            }
            $d = 1 / $d;
            $del = $d * $c;
            $h = $h * $del;
            if (abs($del - 1) < self::EPS) {
                break;
            }
        }

        return $h * exp(-$x + $a * log($x) - $gln);
    }
}
