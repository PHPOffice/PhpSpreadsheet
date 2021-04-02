<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Binomial
{
    use BaseValidations;

    /**
     * BINOMDIST.
     *
     * Returns the individual term binomial distribution probability. Use BINOMDIST in problems with
     *        a fixed number of tests or trials, when the outcomes of any trial are only success or failure,
     *        when trials are independent, and when the probability of success is constant throughout the
     *        experiment. For example, BINOMDIST can calculate the probability that two of the next three
     *        babies born are male.
     *
     * @param mixed (int) $value Number of successes in trials
     * @param mixed (int) $trials Number of trials
     * @param mixed (float) $probability Probability of success on each trial
     * @param mixed (bool) $cumulative
     *
     * @return float|string
     */
    public static function distribution($value, $trials, $probability, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);

        try {
            $value = self::validateInt($value);
            $trials = self::validateInt($trials);
            $probability = self::validateProbability($probability);
            $cumulative = self::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($value > $trials)) {
            return Functions::NAN();
        }

        if ($cumulative) {
            return self::calculateCumulativeBinomial($value, $trials, $probability);
        }

        return MathTrig::COMBIN($trials, $value) * $probability ** $value * (1 - $probability) ** ($trials - $value);
    }

    /**
     * BINOM.DIST.RANGE.
     *
     * Returns returns the Binomial Distribution probability for the number of successes from a specified number
     *     of trials falling into a specified range.
     *
     * @param mixed (int) $trials Number of trials
     * @param mixed (float) $probability Probability of success on each trial
     * @param mixed (int) $successes The number of successes in trials
     * @param mixed (int) $limit Upper limit for successes in trials
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
            $trials = self::validateInt($trials);
            $probability = self::validateProbability($probability);
            $successes = self::validateInt($successes);
            $limit = self::validateInt($limit);
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
            $summer += MathTrig::COMBIN($trials, $i) * $probability ** $i * (1 - $probability) ** ($trials - $i);
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
     * @param mixed (float) $failures Number of Failures
     * @param mixed (float) $successes Threshold number of Successes
     * @param mixed (float) $probability Probability of success on each trial
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
            $failures = self::validateInt($failures);
            $successes = self::validateInt($successes);
            $probability = self::validateProbability($probability);
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

        return (MathTrig::COMBIN($failures + $successes - 1, $successes - 1)) *
            ($probability ** $successes) * ((1 - $probability) ** $failures);
    }

    /**
     * CRITBINOM.
     *
     * Returns the smallest value for which the cumulative binomial distribution is greater
     *        than or equal to a criterion value
     *
     * @param float $trials number of Bernoulli trials
     * @param float $probability probability of a success on each trial
     * @param float $alpha criterion value
     *
     * @return int|string
     */
    public static function inverse($trials, $probability, $alpha)
    {
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);

        try {
            $trials = self::validateInt($trials);
            $probability = self::validateProbability($probability);
            $alpha = self::validateFloat($alpha);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($trials < 0) {
            return Functions::NAN();
        } elseif (($alpha < 0.0) || ($alpha > 1.0)) {
            return Functions::NAN();
        }

        $successes = 0;
        while (true && $successes <= $trials) {
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
            $summer += MathTrig::COMBIN($trials, $i) * $probability ** $i * (1 - $probability) ** ($trials - $i);
        }

        return $summer;
    }
}
