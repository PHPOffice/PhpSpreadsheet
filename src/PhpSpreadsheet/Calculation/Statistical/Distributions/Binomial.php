<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations;

class Binomial
{
    use ArrayEnabled;

    /**
     * BINOMDIST.
     *
     * Returns the individual term binomial distribution probability. Use BINOMDIST in problems with
     *        a fixed number of tests or trials, when the outcomes of any trial are only success or failure,
     *        when trials are independent, and when the probability of success is constant throughout the
     *        experiment. For example, BINOMDIST can calculate the probability that two of the next three
     *        babies born are male.
     *
     * @param mixed $value Integer number of successes in trials
     *                      Or can be an array of values
     * @param mixed $trials Integer umber of trials
     *                      Or can be an array of values
     * @param mixed $probability Probability of success on each trial as a float
     *                      Or can be an array of values
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function distribution(mixed $value, mixed $trials, mixed $probability, mixed $cumulative)
    {
        if (is_array($value) || is_array($trials) || is_array($probability) || is_array($cumulative)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $trials, $probability, $cumulative);
        }

        try {
            $value = DistributionValidations::validateInt($value);
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($value > $trials)) {
            return ExcelError::NAN();
        }

        if ($cumulative) {
            return self::calculateCumulativeBinomial($value, $trials, $probability);
        }
        /** @var float $comb */
        $comb = Combinations::withoutRepetition($trials, $value);

        return $comb * $probability ** $value
            * (1 - $probability) ** ($trials - $value);
    }

    /**
     * BINOM.DIST.RANGE.
     *
     * Returns returns the Binomial Distribution probability for the number of successes from a specified number
     *     of trials falling into a specified range.
     *
     * @param mixed $trials Integer number of trials
     *                      Or can be an array of values
     * @param mixed $probability Probability of success on each trial as a float
     *                      Or can be an array of values
     * @param mixed $successes The integer number of successes in trials
     *                      Or can be an array of values
     * @param mixed $limit Upper limit for successes in trials as null, or an integer
     *                           If null, then this will indicate the same as the number of Successes
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|int|string If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function range(mixed $trials, mixed $probability, mixed $successes, mixed $limit = null): array|string|float|int
    {
        if (is_array($trials) || is_array($probability) || is_array($successes) || is_array($limit)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $trials, $probability, $successes, $limit);
        }

        $limit = $limit ?? $successes;

        try {
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $successes = DistributionValidations::validateInt($successes);
            $limit = DistributionValidations::validateInt($limit);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($successes < 0) || ($successes > $trials)) {
            return ExcelError::NAN();
        }
        if (($limit < 0) || ($limit > $trials) || $limit < $successes) {
            return ExcelError::NAN();
        }

        $summer = 0;
        for ($i = $successes; $i <= $limit; ++$i) {
            /** @var float $comb */
            $comb = Combinations::withoutRepetition($trials, $i);
            $summer += $comb * $probability ** $i
                * (1 - $probability) ** ($trials - $i);
        }

        return $summer;
    }

    /**
     * NEGBINOMDIST.
     *
     * Returns the negative binomial distribution. NEGBINOMDIST returns the probability that
     *        there will be number_f failures before the number_s-th success, when the constant
     *        probability of a success is probability_s. This function is similar to the binomial
     *        distribution, except that the number of successes is fixed, and the number of trials is
     *        variable. Like the binomial, trials are assumed to be independent.
     *
     * @param mixed $failures Number of Failures as an integer
     *                      Or can be an array of values
     * @param mixed $successes Threshold number of Successes as an integer
     *                      Or can be an array of values
     * @param mixed $probability Probability of success on each trial as a float
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     *
     * TODO Add support for the cumulative flag not present for NEGBINOMDIST, but introduced for NEGBINOM.DIST
     *      The cumulative default should be false to reflect the behaviour of NEGBINOMDIST
     */
    public static function negative(mixed $failures, mixed $successes, mixed $probability): array|string|float
    {
        if (is_array($failures) || is_array($successes) || is_array($probability)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $failures, $successes, $probability);
        }

        try {
            $failures = DistributionValidations::validateInt($failures);
            $successes = DistributionValidations::validateInt($successes);
            $probability = DistributionValidations::validateProbability($probability);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($failures < 0) || ($successes < 1)) {
            return ExcelError::NAN();
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            if (($failures + $successes - 1) <= 0) {
                return ExcelError::NAN();
            }
        }
        /** @var float $comb */
        $comb = Combinations::withoutRepetition($failures + $successes - 1, $successes - 1);

        return $comb
            * ($probability ** $successes) * ((1 - $probability) ** $failures);
    }

    /**
     * BINOM.INV.
     *
     * Returns the smallest value for which the cumulative binomial distribution is greater
     *        than or equal to a criterion value
     *
     * @param mixed $trials number of Bernoulli trials as an integer
     *                      Or can be an array of values
     * @param mixed $probability probability of a success on each trial as a float
     *                      Or can be an array of values
     * @param mixed $alpha criterion value as a float
     *                      Or can be an array of values
     *
     * @return array<mixed>|int|string If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function inverse(mixed $trials, mixed $probability, mixed $alpha): array|string|int
    {
        if (is_array($trials) || is_array($probability) || is_array($alpha)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $trials, $probability, $alpha);
        }

        try {
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $alpha = DistributionValidations::validateFloat($alpha);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($trials < 0) {
            return ExcelError::NAN();
        } elseif (($alpha < 0.0) || ($alpha > 1.0)) {
            return ExcelError::NAN();
        }

        $successes = 0;
        while ($successes <= $trials) {
            $result = self::calculateCumulativeBinomial($successes, $trials, $probability);
            if ($result >= $alpha) {
                break;
            }
            ++$successes;
        }

        return $successes;
    }

    private static function calculateCumulativeBinomial(int $value, int $trials, float $probability): float|int
    {
        $summer = 0;
        for ($i = 0; $i <= $value; ++$i) {
            /** @var float $comb */
            $comb = Combinations::withoutRepetition($trials, $i);
            $summer += $comb * $probability ** $i
                * (1 - $probability) ** ($trials - $i);
        }

        return $summer;
    }
}
