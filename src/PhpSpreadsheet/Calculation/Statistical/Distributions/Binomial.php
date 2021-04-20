<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations;

class Binomial
{
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
     * @param mixed $trials Integer umber of trials
     * @param mixed $probability Probability of success on each trial as a float
     * @param mixed $cumulative Boolean value indicating if we want the cdf (true) or the pdf (false)
     *
     * @return float|string
     */
    public static function distribution($value, $trials, $probability, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);

        try {
            $value = DistributionValidations::validateInt($value);
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($value > $trials)) {
            return Functions::NAN();
        }

        if ($cumulative) {
            return self::calculateCumulativeBinomial($value, $trials, $probability);
        }

        return Combinations::withoutRepetition($trials, $value) * $probability ** $value
            * (1 - $probability) ** ($trials - $value);
    }

    /**
     * BINOM.DIST.RANGE.
     *
     * Returns returns the Binomial Distribution probability for the number of successes from a specified number
     *     of trials falling into a specified range.
     *
     * @param mixed $trials Integer number of trials
     * @param mixed $probability Probability of success on each trial as a float
     * @param mixed $successes The integer number of successes in trials
     * @param mixed $limit Upper limit for successes in trials as null, or an integer
     *                           If null, then this will indicate the same as the number of Successes
     *
     * @return float|string
     */
    public static function range($trials, $probability, $successes, $limit = null)
    {
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);
        $successes = Functions::flattenSingleValue($successes);
        $limit = ($limit === null) ? $successes : Functions::flattenSingleValue($limit);

        try {
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $successes = DistributionValidations::validateInt($successes);
            $limit = DistributionValidations::validateInt($limit);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($successes < 0) || ($successes > $trials)) {
            return Functions::NAN();
        }
        if (($limit < 0) || ($limit > $trials) || $limit < $successes) {
            return Functions::NAN();
        }

        $summer = 0;
        for ($i = $successes; $i <= $limit; ++$i) {
            $summer += Combinations::withoutRepetition($trials, $i) * $probability ** $i
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
     * @param mixed $successes Threshold number of Successes as an integer
     * @param mixed $probability Probability of success on each trial as a float
     *
     * @return float|string The result, or a string containing an error
     *
     * TODO Add support for the cumulative flag not present for NEGBINOMDIST, but introduced for NEGBINOM.DIST
     *      The cumulative default should be false to reflect the behaviour of NEGBINOMDIST
     */
    public static function negative($failures, $successes, $probability)
    {
        $failures = Functions::flattenSingleValue($failures);
        $successes = Functions::flattenSingleValue($successes);
        $probability = Functions::flattenSingleValue($probability);

        try {
            $failures = DistributionValidations::validateInt($failures);
            $successes = DistributionValidations::validateInt($successes);
            $probability = DistributionValidations::validateProbability($probability);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($failures < 0) || ($successes < 1)) {
            return Functions::NAN();
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            if (($failures + $successes - 1) <= 0) {
                return Functions::NAN();
            }
        }

        return (Combinations::withoutRepetition($failures + $successes - 1, $successes - 1))
            * ($probability ** $successes) * ((1 - $probability) ** $failures);
    }

    /**
     * CRITBINOM.
     *
     * Returns the smallest value for which the cumulative binomial distribution is greater
     *        than or equal to a criterion value
     *
     * @param mixed $trials number of Bernoulli trials as an integer
     * @param mixed $probability probability of a success on each trial as a float
     * @param mixed $alpha criterion value as a float
     *
     * @return int|string
     */
    public static function inverse($trials, $probability, $alpha)
    {
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);

        try {
            $trials = DistributionValidations::validateInt($trials);
            $probability = DistributionValidations::validateProbability($probability);
            $alpha = DistributionValidations::validateFloat($alpha);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($trials < 0) {
            return Functions::NAN();
        } elseif (($alpha < 0.0) || ($alpha > 1.0)) {
            return Functions::NAN();
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

    /**
     * @return float|int
     */
    private static function calculateCumulativeBinomial(int $value, int $trials, float $probability)
    {
        $summer = 0;
        for ($i = 0; $i <= $value; ++$i) {
            $summer += Combinations::withoutRepetition($trials, $i) * $probability ** $i
                * (1 - $probability) ** ($trials - $i);
        }

        return $summer;
    }
}
