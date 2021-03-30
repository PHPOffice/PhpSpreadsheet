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
            $probability = self::validateFloat($probability);
            $cumulative = self::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($value < 0) || ($value > $trials)) {
            return Functions::NAN();
        }
        if (($probability < 0.0) || ($probability > 1.0)) {
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
            $probability = self::validateFloat($probability);
            $successes = self::validateInt($successes);
            $limit = self::validateInt($limit);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($probability < 0.0) || ($probability > 1.0)) {
            return Functions::NAN();
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
            $probability = self::validateFloat($probability);
            $alpha = self::validateFloat($alpha);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($trials < 0) {
            return Functions::NAN();
        } elseif (($probability < 0.0) || ($probability > 1.0)) {
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
