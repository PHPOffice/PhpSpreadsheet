<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances;

class Statistical
{
    const LOG_GAMMA_X_MAX_VALUE = 2.55e305;
    const EPS = 2.22e-16;
    const MAX_VALUE = 1.2e308;
    const MAX_ITERATIONS = 256;
    const SQRT2PI = 2.5066282746310005024157652848110452530069867406099;

    /*
     *                                inverse_ncdf.php
     *                            -------------------
     *    begin                : Friday, January 16, 2004
     *    copyright            : (C) 2004 Michael Nickerson
     *    email                : nickersonm@yahoo.com
     *
     */
    private static function inverseNcdf($p)
    {
        //    Inverse ncdf approximation by Peter J. Acklam, implementation adapted to
        //    PHP by Michael Nickerson, using Dr. Thomas Ziegler's C implementation as
        //    a guide. http://home.online.no/~pjacklam/notes/invnorm/index.html
        //    I have not checked the accuracy of this implementation. Be aware that PHP
        //    will truncate the coeficcients to 14 digits.

        //    You have permission to use and distribute this function freely for
        //    whatever purpose you want, but please show common courtesy and give credit
        //    where credit is due.

        //    Input paramater is $p - probability - where 0 < p < 1.

        //    Coefficients in rational approximations
        static $a = [
            1 => -3.969683028665376e+01,
            2 => 2.209460984245205e+02,
            3 => -2.759285104469687e+02,
            4 => 1.383577518672690e+02,
            5 => -3.066479806614716e+01,
            6 => 2.506628277459239e+00,
        ];

        static $b = [
            1 => -5.447609879822406e+01,
            2 => 1.615858368580409e+02,
            3 => -1.556989798598866e+02,
            4 => 6.680131188771972e+01,
            5 => -1.328068155288572e+01,
        ];

        static $c = [
            1 => -7.784894002430293e-03,
            2 => -3.223964580411365e-01,
            3 => -2.400758277161838e+00,
            4 => -2.549732539343734e+00,
            5 => 4.374664141464968e+00,
            6 => 2.938163982698783e+00,
        ];

        static $d = [
            1 => 7.784695709041462e-03,
            2 => 3.224671290700398e-01,
            3 => 2.445134137142996e+00,
            4 => 3.754408661907416e+00,
        ];

        //    Define lower and upper region break-points.
        $p_low = 0.02425; //Use lower region approx. below this
        $p_high = 1 - $p_low; //Use upper region approx. above this

        if (0 < $p && $p < $p_low) {
            //    Rational approximation for lower region.
            $q = sqrt(-2 * log($p));

            return ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                    (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        } elseif ($p_low <= $p && $p <= $p_high) {
            //    Rational approximation for central region.
            $q = $p - 0.5;
            $r = $q * $q;

            return ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $r + $a[6]) * $q /
                   ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + $b[5]) * $r + 1);
        } elseif ($p_high < $p && $p < 1) {
            //    Rational approximation for upper region.
            $q = sqrt(-2 * log(1 - $p));

            return -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                     (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        }
        //    If 0 < p < 1, return a null value
        return Functions::NULL();
    }

    /**
     * AVEDEV.
     *
     * Returns the average of the absolute deviations of data points from their mean.
     * AVEDEV is a measure of the variability in a data set.
     *
     * Excel Function:
     *        AVEDEV(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     *
     *@see Statistical\Averages::averageDeviations()
     *      Use the averageDeviations() method in the Statistical\Averages class instead
     */
    public static function AVEDEV(...$args)
    {
        return Averages::averageDeviations(...$args);
    }

    /**
     * AVERAGE.
     *
     * Returns the average (arithmetic mean) of the arguments
     *
     * Excel Function:
     *        AVERAGE(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Averages::average()
     *      Use the average() method in the Statistical\Averages class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function AVERAGE(...$args)
    {
        return Averages::average(...$args);
    }

    /**
     * AVERAGEA.
     *
     * Returns the average of its arguments, including numbers, text, and logical values
     *
     * Excel Function:
     *        AVERAGEA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     *
     *@see Statistical\Averages::averageA()
     *      Use the averageA() method in the Statistical\Averages class instead
     */
    public static function AVERAGEA(...$args)
    {
        return Averages::averageA(...$args);
    }

    /**
     * AVERAGEIF.
     *
     * Returns the average value from a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        AVERAGEIF(value1[,value2[, ...]],condition)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::AVERAGEIF()
     *      Use the AVERAGEIF() method in the Statistical\Conditional class instead
     *
     * @param mixed $range Data values
     * @param string $condition the criteria that defines which cells will be checked
     * @param mixed[] $averageRange Data values
     *
     * @return null|float|string
     */
    public static function AVERAGEIF($range, $condition, $averageRange = [])
    {
        return Conditional::AVERAGEIF($range, $condition, $averageRange);
    }

    /**
     * BETADIST.
     *
     * Returns the beta distribution.
     *
     * @Deprecated 1.18.0
     *
     *@see Statistical\Distributions\Beta::distribution()
     *      Use the distribution() method in the Statistical\Distributions\Beta class instead
     *
     * @param float $value Value at which you want to evaluate the distribution
     * @param float $alpha Parameter to the distribution
     * @param float $beta Parameter to the distribution
     * @param mixed $rMin
     * @param mixed $rMax
     *
     * @return float|string
     */
    public static function BETADIST($value, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        return Statistical\Distributions\Beta::distribution($value, $alpha, $beta, $rMin, $rMax);
    }

    /**
     * BETAINV.
     *
     * Returns the inverse of the Beta distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Beta::inverse()
     *      Use the inverse() method in the Statistical\Distributions\Beta class instead
     *
     * @param float $probability Probability at which you want to evaluate the distribution
     * @param float $alpha Parameter to the distribution
     * @param float $beta Parameter to the distribution
     * @param float $rMin Minimum value
     * @param float $rMax Maximum value
     *
     * @return float|string
     */
    public static function BETAINV($probability, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        return Statistical\Distributions\Beta::inverse($probability, $alpha, $beta, $rMin, $rMax);
    }

    /**
     * BINOMDIST.
     *
     * Returns the individual term binomial distribution probability. Use BINOMDIST in problems with
     *        a fixed number of tests or trials, when the outcomes of any trial are only success or failure,
     *        when trials are independent, and when the probability of success is constant throughout the
     *        experiment. For example, BINOMDIST can calculate the probability that two of the next three
     *        babies born are male.
     *
     * @param mixed (float) $value Number of successes in trials
     * @param mixed (float) $trials Number of trials
     * @param mixed (float) $probability Probability of success on each trial
     * @param mixed (bool) $cumulative
     *
     * @return float|string
     */
    public static function BINOMDIST($value, $trials, $probability, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);

        if ((is_numeric($value)) && (is_numeric($trials)) && (is_numeric($probability))) {
            $value = floor($value);
            $trials = floor($trials);
            if (($value < 0) || ($value > $trials)) {
                return Functions::NAN();
            }
            if (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    $summer = 0;
                    for ($i = 0; $i <= $value; ++$i) {
                        $summer += MathTrig::COMBIN($trials, $i) * $probability ** $i * (1 - $probability) ** ($trials - $i);
                    }

                    return $summer;
                }

                return MathTrig::COMBIN($trials, $value) * $probability ** $value * (1 - $probability) ** ($trials - $value);
            }
        }

        return Functions::VALUE();
    }

    /**
     * CHIDIST.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\ChiSquared::distributionRightTail()
     *      Use the distributionRightTail() method in the Statistical\Distributions\ChiSquared class instead
     *
     * @param float $value Value for the function
     * @param float $degrees degrees of freedom
     *
     * @return float|string
     */
    public static function CHIDIST($value, $degrees)
    {
        return Statistical\Distributions\ChiSquared::distributionRightTail($value, $degrees);
    }

    /**
     * CHIINV.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\ChiSquared::inverseRightTail()
     *      Use the inverseRightTail() method in the Statistical\Distributions\ChiSquared class instead
     *
     * @param float $probability Probability for the function
     * @param float $degrees degrees of freedom
     *
     * @return float|string
     */
    public static function CHIINV($probability, $degrees)
    {
        return Statistical\Distributions\ChiSquared::inverseRightTail($probability, $degrees);
    }

    /**
     * CONFIDENCE.
     *
     * Returns the confidence interval for a population mean
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Confidence::CONFIDENCE()
     *      Use the CONFIDENCE() method in the Statistical\Confidence class instead
     *
     * @param float $alpha
     * @param float $stdDev Standard Deviation
     * @param float $size
     *
     * @return float|string
     */
    public static function CONFIDENCE($alpha, $stdDev, $size)
    {
        return Confidence::CONFIDENCE($alpha, $stdDev, $size);
    }

    /**
     * CORREL.
     *
     * Returns covariance, the average of the products of deviations for each data point pair.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::CORREL()
     *      Use the CORREL() method in the Statistical\Trends class instead
     *
     * @param mixed $yValues array of mixed Data Series Y
     * @param null|mixed $xValues array of mixed Data Series X
     *
     * @return float|string
     */
    public static function CORREL($yValues, $xValues = null)
    {
        return Trends::CORREL($xValues, $yValues);
    }

    /**
     * COUNT.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNT(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Counts::COUNT()
     *      Use the COUNT() method in the Statistical\Counts class instead
     *
     * @param mixed ...$args Data values
     *
     * @return int
     */
    public static function COUNT(...$args)
    {
        return Counts::COUNT(...$args);
    }

    /**
     * COUNTA.
     *
     * Counts the number of cells that are not empty within the list of arguments
     *
     * Excel Function:
     *        COUNTA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Counts::COUNTA()
     *      Use the COUNTA() method in the Statistical\Counts class instead
     *
     * @param mixed ...$args Data values
     *
     * @return int
     */
    public static function COUNTA(...$args)
    {
        return Counts::COUNTA(...$args);
    }

    /**
     * COUNTBLANK.
     *
     * Counts the number of empty cells within the list of arguments
     *
     * Excel Function:
     *        COUNTBLANK(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Counts::COUNTBLANK()
     *      Use the COUNTBLANK() method in the Statistical\Counts class instead
     *
     * @param mixed ...$args Data values
     *
     * @return int
     */
    public static function COUNTBLANK(...$args)
    {
        return Counts::COUNTBLANK(...$args);
    }

    /**
     * COUNTIF.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNTIF(range,condition)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::COUNTIF()
     *      Use the COUNTIF() method in the Statistical\Conditional class instead
     *
     * @param mixed $range Data values
     * @param string $condition the criteria that defines which cells will be counted
     *
     * @return int
     */
    public static function COUNTIF($range, $condition)
    {
        return Conditional::COUNTIF($range, $condition);
    }

    /**
     * COUNTIFS.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNTIFS(criteria_range1, criteria1, [criteria_range2, criteria2]…)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::COUNTIFS()
     *      Use the COUNTIFS() method in the Statistical\Conditional class instead
     *
     * @param mixed $args Pairs of Ranges and Criteria
     *
     * @return int
     */
    public static function COUNTIFS(...$args)
    {
        return Conditional::COUNTIFS(...$args);
    }

    /**
     * COVAR.
     *
     * Returns covariance, the average of the products of deviations for each data point pair.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::COVAR()
     *      Use the COVAR() method in the Statistical\Trends class instead
     *
     * @param mixed $yValues array of mixed Data Series Y
     * @param mixed $xValues array of mixed Data Series X
     *
     * @return float|string
     */
    public static function COVAR($yValues, $xValues)
    {
        return Trends::COVAR($yValues, $xValues);
    }

    /**
     * CRITBINOM.
     *
     * Returns the smallest value for which the cumulative binomial distribution is greater
     *        than or equal to a criterion value
     *
     * See https://support.microsoft.com/en-us/help/828117/ for details of the algorithm used
     *
     * @param float $trials number of Bernoulli trials
     * @param float $probability probability of a success on each trial
     * @param float $alpha criterion value
     *
     * @return int|string
     *
     * @TODO    Warning. This implementation differs from the algorithm detailed on the MS
     *            web site in that $CumPGuessMinus1 = $CumPGuess - 1 rather than $CumPGuess - $PGuess
     *            This eliminates a potential endless loop error, but may have an adverse affect on the
     *            accuracy of the function (although all my tests have so far returned correct results).
     */
    public static function CRITBINOM($trials, $probability, $alpha)
    {
        $trials = floor(Functions::flattenSingleValue($trials));
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);

        if ((is_numeric($trials)) && (is_numeric($probability)) && (is_numeric($alpha))) {
            $trials = (int) $trials;
            if ($trials < 0) {
                return Functions::NAN();
            } elseif (($probability < 0.0) || ($probability > 1.0)) {
                return Functions::NAN();
            } elseif (($alpha < 0.0) || ($alpha > 1.0)) {
                return Functions::NAN();
            }

            if ($alpha <= 0.5) {
                $t = sqrt(log(1 / ($alpha * $alpha)));
                $trialsApprox = 0 - ($t + (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t));
            } else {
                $t = sqrt(log(1 / (1 - $alpha) ** 2));
                $trialsApprox = $t - (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t);
            }

            $Guess = floor($trials * $probability + $trialsApprox * sqrt($trials * $probability * (1 - $probability)));
            if ($Guess < 0) {
                $Guess = 0;
            } elseif ($Guess > $trials) {
                $Guess = $trials;
            }

            $TotalUnscaledProbability = $UnscaledPGuess = $UnscaledCumPGuess = 0.0;
            $EssentiallyZero = 10e-12;

            $m = floor($trials * $probability);
            ++$TotalUnscaledProbability;
            if ($m == $Guess) {
                ++$UnscaledPGuess;
            }
            if ($m <= $Guess) {
                ++$UnscaledCumPGuess;
            }

            $PreviousValue = 1;
            $Done = false;
            $k = $m + 1;
            while ((!$Done) && ($k <= $trials)) {
                $CurrentValue = $PreviousValue * ($trials - $k + 1) * $probability / ($k * (1 - $probability));
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                ++$k;
            }

            $PreviousValue = 1;
            $Done = false;
            $k = $m - 1;
            while ((!$Done) && ($k >= 0)) {
                $CurrentValue = $PreviousValue * $k + 1 * (1 - $probability) / (($trials - $k) * $probability);
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                --$k;
            }

            $PGuess = $UnscaledPGuess / $TotalUnscaledProbability;
            $CumPGuess = $UnscaledCumPGuess / $TotalUnscaledProbability;

            $CumPGuessMinus1 = $CumPGuess - 1;

            while (true) {
                if (($CumPGuessMinus1 < $alpha) && ($CumPGuess >= $alpha)) {
                    return $Guess;
                } elseif (($CumPGuessMinus1 < $alpha) && ($CumPGuess < $alpha)) {
                    $PGuessPlus1 = $PGuess * ($trials - $Guess) * $probability / $Guess / (1 - $probability);
                    $CumPGuessMinus1 = $CumPGuess;
                    $CumPGuess = $CumPGuess + $PGuessPlus1;
                    $PGuess = $PGuessPlus1;
                    ++$Guess;
                } elseif (($CumPGuessMinus1 >= $alpha) && ($CumPGuess >= $alpha)) {
                    $PGuessMinus1 = $PGuess * $Guess * (1 - $probability) / ($trials - $Guess + 1) / $probability;
                    $CumPGuess = $CumPGuessMinus1;
                    $CumPGuessMinus1 = $CumPGuessMinus1 - $PGuess;
                    $PGuess = $PGuessMinus1;
                    --$Guess;
                }
            }
        }

        return Functions::VALUE();
    }

    /**
     * DEVSQ.
     *
     * Returns the sum of squares of deviations of data points from their sample mean.
     *
     * Excel Function:
     *        DEVSQ(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function DEVSQ(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        // Return value
        $returnValue = null;

        $aMean = Averages::average($aArgs);
        if ($aMean != Functions::DIV0()) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                // Is it a numeric value?
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) ||
                    (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    if ($returnValue === null) {
                        $returnValue = ($arg - $aMean) ** 2;
                    } else {
                        $returnValue += ($arg - $aMean) ** 2;
                    }
                    ++$aCount;
                }
            }

            // Return
            if ($returnValue === null) {
                return Functions::NAN();
            }

            return $returnValue;
        }

        return Functions::NA();
    }

    /**
     * EXPONDIST.
     *
     *    Returns the exponential distribution. Use EXPONDIST to model the time between events,
     *        such as how long an automated bank teller takes to deliver cash. For example, you can
     *        use EXPONDIST to determine the probability that the process takes at most 1 minute.
     *
     * @param float $value Value of the function
     * @param float $lambda The parameter value
     * @param bool $cumulative
     *
     * @return float|string
     */
    public static function EXPONDIST($value, $lambda, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $lambda = Functions::flattenSingleValue($lambda);
        $cumulative = Functions::flattenSingleValue($cumulative);

        if ((is_numeric($value)) && (is_numeric($lambda))) {
            if (($value < 0) || ($lambda < 0)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return 1 - exp(0 - $value * $lambda);
                }

                return $lambda * exp(0 - $value * $lambda);
            }
        }

        return Functions::VALUE();
    }

    /**
     * F.DIST.
     *
     *    Returns the F probability distribution.
     *    You can use this function to determine whether two data sets have different degrees of diversity.
     *    For example, you can examine the test scores of men and women entering high school, and determine
     *        if the variability in the females is different from that found in the males.
     *
     * @param float $value Value of the function
     * @param int $u The numerator degrees of freedom
     * @param int $v The denominator degrees of freedom
     * @param bool $cumulative If cumulative is TRUE, F.DIST returns the cumulative distribution function;
     *                         if FALSE, it returns the probability density function.
     *
     * @return float|string
     */
    public static function FDIST2($value, $u, $v, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $u = Functions::flattenSingleValue($u);
        $v = Functions::flattenSingleValue($v);
        $cumulative = Functions::flattenSingleValue($cumulative);

        if (is_numeric($value) && is_numeric($u) && is_numeric($v)) {
            if ($value < 0 || $u < 1 || $v < 1) {
                return Functions::NAN();
            }

            $cumulative = (bool) $cumulative;
            $u = (int) $u;
            $v = (int) $v;

            if ($cumulative) {
                $adjustedValue = ($u * $value) / ($u * $value + $v);

                return Statistical\Distributions\Beta::incompleteBeta($adjustedValue, $u / 2, $v / 2);
            }

            return (Statistical\Distributions\Gamma::gammaValue(($v + $u) / 2) /
                    (Statistical\Distributions\Gamma::gammaValue($u / 2) *
                        Statistical\Distributions\Gamma::gammaValue($v / 2))) *
                (($u / $v) ** ($u / 2)) *
                (($value ** (($u - 2) / 2)) / ((1 + ($u / $v) * $value) ** (($u + $v) / 2)));
        }

        return Functions::VALUE();
    }

    /**
     * FISHER.
     *
     * Returns the Fisher transformation at x. This transformation produces a function that
     *        is normally distributed rather than skewed. Use this function to perform hypothesis
     *        testing on the correlation coefficient.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Fisher::distribution()
     *      Use the distribution() method in the Statistical\Distributions\Fisher class instead
     *
     * @param float $value
     *
     * @return float|string
     */
    public static function FISHER($value)
    {
        return Statistical\Distributions\Fisher::distribution($value);
    }

    /**
     * FISHERINV.
     *
     * Returns the inverse of the Fisher transformation. Use this transformation when
     *        analyzing correlations between ranges or arrays of data. If y = FISHER(x), then
     *        FISHERINV(y) = x.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Fisher::inverse()
     *      Use the inverse() method in the Statistical\Distributions\Fisher class instead
     *
     * @param float $value
     *
     * @return float|string
     */
    public static function FISHERINV($value)
    {
        return Statistical\Distributions\Fisher::inverse($value);
    }

    /**
     * FORECAST.
     *
     * Calculates, or predicts, a future value by using existing values. The predicted value is a y-value for a given x-value.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::FORECAST()
     *      Use the FORECAST() method in the Statistical\Trends class instead
     *
     * @param float $xValue Value of X for which we want to find Y
     * @param mixed $yValues array of mixed Data Series Y
     * @param mixed $xValues of mixed Data Series X
     *
     * @return bool|float|string
     */
    public static function FORECAST($xValue, $yValues, $xValues)
    {
        return Trends::FORECAST($xValue, $yValues, $xValues);
    }

    /**
     * GAMMA.
     *
     * Returns the gamma function value.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Gamma::gamma()
     *      Use the gamma() method in the Statistical\Distributions\Gamma class instead
     *
     * @param float $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function GAMMAFunction($value)
    {
        return Statistical\Distributions\Gamma::gamma($value);
    }

    /**
     * GAMMADIST.
     *
     * Returns the gamma distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Gamma::distribution()
     *      Use the distribution() method in the Statistical\Distributions\Gamma class instead
     *
     * @param float $value Value at which you want to evaluate the distribution
     * @param float $a Parameter to the distribution
     * @param float $b Parameter to the distribution
     * @param bool $cumulative
     *
     * @return float|string
     */
    public static function GAMMADIST($value, $a, $b, $cumulative)
    {
        return Statistical\Distributions\Gamma::distribution($value, $a, $b, $cumulative);
    }

    /**
     * GAMMAINV.
     *
     * Returns the inverse of the Gamma distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Gamma::inverse()
     *      Use the inverse() method in the Statistical\Distributions\Gamma class instead
     *
     * @param float $probability Probability at which you want to evaluate the distribution
     * @param float $alpha Parameter to the distribution
     * @param float $beta Parameter to the distribution
     *
     * @return float|string
     */
    public static function GAMMAINV($probability, $alpha, $beta)
    {
        return Statistical\Distributions\Gamma::inverse($probability, $alpha, $beta);
    }

    /**
     * GAMMALN.
     *
     * Returns the natural logarithm of the gamma function.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Gamma::ln()
     *      Use the ln() method in the Statistical\Distributions\Gamma class instead
     *
     * @param float $value
     *
     * @return float|string
     */
    public static function GAMMALN($value)
    {
        return Statistical\Distributions\Gamma::ln($value);
    }

    /**
     * GAUSS.
     *
     * Calculates the probability that a member of a standard normal population will fall between
     *     the mean and z standard deviations from the mean.
     *
     * @param float $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function GAUSS($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }

        return self::NORMDIST($value, 0, 1, true) - 0.5;
    }

    /**
     * GEOMEAN.
     *
     * Returns the geometric mean of an array or range of positive data. For example, you
     *        can use GEOMEAN to calculate average growth rate given compound interest with
     *        variable rates.
     *
     * Excel Function:
     *        GEOMEAN(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function GEOMEAN(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        $aMean = MathTrig\Product::funcProduct($aArgs);
        if (is_numeric($aMean) && ($aMean > 0)) {
            $aCount = Counts::COUNT($aArgs);
            if (Minimum::MIN($aArgs) > 0) {
                return $aMean ** (1 / $aCount);
            }
        }

        return Functions::NAN();
    }

    /**
     * GROWTH.
     *
     * Returns values along a predicted exponential Trend
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::GROWTH()
     *      Use the GROWTH() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     * @param mixed[] $newValues Values of X for which we want to find Y
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     *
     * @return array of float
     */
    public static function GROWTH($yValues, $xValues = [], $newValues = [], $const = true)
    {
        return Trends::GROWTH($yValues, $xValues, $newValues, $const);
    }

    /**
     * HARMEAN.
     *
     * Returns the harmonic mean of a data set. The harmonic mean is the reciprocal of the
     *        arithmetic mean of reciprocals.
     *
     * Excel Function:
     *        HARMEAN(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function HARMEAN(...$args)
    {
        // Return value
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);
        if (Minimum::MIN($aArgs) < 0) {
            return Functions::NAN();
        }
        $aCount = 0;
        foreach ($aArgs as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($arg <= 0) {
                    return Functions::NAN();
                }
                $returnValue += (1 / $arg);
                ++$aCount;
            }
        }

        // Return
        if ($aCount > 0) {
            return 1 / ($returnValue / $aCount);
        }

        return Functions::NA();
    }

    /**
     * HYPGEOMDIST.
     *
     * Returns the hypergeometric distribution. HYPGEOMDIST returns the probability of a given number of
     * sample successes, given the sample size, population successes, and population size.
     *
     * @param float $sampleSuccesses Number of successes in the sample
     * @param float $sampleNumber Size of the sample
     * @param float $populationSuccesses Number of successes in the population
     * @param float $populationNumber Population size
     *
     * @return float|string
     */
    public static function HYPGEOMDIST($sampleSuccesses, $sampleNumber, $populationSuccesses, $populationNumber)
    {
        $sampleSuccesses = Functions::flattenSingleValue($sampleSuccesses);
        $sampleNumber = Functions::flattenSingleValue($sampleNumber);
        $populationSuccesses = Functions::flattenSingleValue($populationSuccesses);
        $populationNumber = Functions::flattenSingleValue($populationNumber);

        if ((is_numeric($sampleSuccesses)) && (is_numeric($sampleNumber)) && (is_numeric($populationSuccesses)) && (is_numeric($populationNumber))) {
            $sampleSuccesses = floor($sampleSuccesses);
            $sampleNumber = floor($sampleNumber);
            $populationSuccesses = floor($populationSuccesses);
            $populationNumber = floor($populationNumber);

            if (($sampleSuccesses < 0) || ($sampleSuccesses > $sampleNumber) || ($sampleSuccesses > $populationSuccesses)) {
                return Functions::NAN();
            }
            if (($sampleNumber <= 0) || ($sampleNumber > $populationNumber)) {
                return Functions::NAN();
            }
            if (($populationSuccesses <= 0) || ($populationSuccesses > $populationNumber)) {
                return Functions::NAN();
            }

            return MathTrig::COMBIN($populationSuccesses, $sampleSuccesses) *
                   MathTrig::COMBIN($populationNumber - $populationSuccesses, $sampleNumber - $sampleSuccesses) /
                   MathTrig::COMBIN($populationNumber, $sampleNumber);
        }

        return Functions::VALUE();
    }

    /**
     * INTERCEPT.
     *
     * Calculates the point at which a line will intersect the y-axis by using existing x-values and y-values.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::INTERCEPT()
     *      Use the INTERCEPT() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string
     */
    public static function INTERCEPT($yValues, $xValues)
    {
        return Trends::INTERCEPT($yValues, $xValues);
    }

    /**
     * KURT.
     *
     * Returns the kurtosis of a data set. Kurtosis characterizes the relative peakedness
     * or flatness of a distribution compared with the normal distribution. Positive
     * kurtosis indicates a relatively peaked distribution. Negative kurtosis indicates a
     * relatively flat distribution.
     *
     * @param array ...$args Data Series
     *
     * @return float|string
     */
    public static function KURT(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = Averages::average($aArgs);
        $stdDev = StandardDeviations::STDEV($aArgs);

        if ($stdDev > 0) {
            $count = $summer = 0;
            // Loop through arguments
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    (!Functions::isMatrixValue($k))
                ) {
                } else {
                    // Is it a numeric value?
                    if ((is_numeric($arg)) && (!is_string($arg))) {
                        $summer += (($arg - $mean) / $stdDev) ** 4;
                        ++$count;
                    }
                }
            }

            // Return
            if ($count > 3) {
                return $summer * ($count * ($count + 1) / (($count - 1) * ($count - 2) * ($count - 3))) - (3 * ($count - 1) ** 2 / (($count - 2) * ($count - 3)));
            }
        }

        return Functions::DIV0();
    }

    /**
     * LARGE.
     *
     * Returns the nth largest value in a data set. You can use this function to
     *        select a value based on its relative standing.
     *
     * Excel Function:
     *        LARGE(value1[,value2[, ...]],entry)
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function LARGE(...$args)
    {
        $aArgs = Functions::flattenArray($args);
        $entry = array_pop($aArgs);

        if ((is_numeric($entry)) && (!is_string($entry))) {
            $entry = (int) floor($entry);

            // Calculate
            $mArgs = [];
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $count = Counts::COUNT($mArgs);
            --$entry;
            if (($entry < 0) || ($entry >= $count) || ($count == 0)) {
                return Functions::NAN();
            }
            rsort($mArgs);

            return $mArgs[$entry];
        }

        return Functions::VALUE();
    }

    /**
     * LINEST.
     *
     * Calculates the statistics for a line by using the "least squares" method to calculate a straight line that best fits your data,
     *        and then returns an array that describes the line.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::LINEST()
     *      Use the LINEST() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param null|mixed[] $xValues Data Series X
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     * @param bool $stats a logical value specifying whether to return additional regression statistics
     *
     * @return array|int|string The result, or a string containing an error
     */
    public static function LINEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        return Trends::LINEST($yValues, $xValues, $const, $stats);
    }

    /**
     * LOGEST.
     *
     * Calculates an exponential curve that best fits the X and Y data series,
     *        and then returns an array that describes the line.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::LOGEST()
     *      Use the LOGEST() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param null|mixed[] $xValues Data Series X
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     * @param bool $stats a logical value specifying whether to return additional regression statistics
     *
     * @return array|int|string The result, or a string containing an error
     */
    public static function LOGEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        return Trends::LOGEST($yValues, $xValues, $const, $stats);
    }

    /**
     * LOGINV.
     *
     * Returns the inverse of the normal cumulative distribution
     *
     * @param float $probability
     * @param float $mean
     * @param float $stdDev
     *
     * @return float|string The result, or a string containing an error
     *
     * @TODO    Try implementing P J Acklam's refinement algorithm for greater
     *            accuracy if I can get my head round the mathematics
     *            (as described at) http://home.online.no/~pjacklam/notes/invnorm/
     */
    public static function LOGINV($probability, $mean, $stdDev)
    {
        $probability = Functions::flattenSingleValue($probability);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            return exp($mean + $stdDev * self::NORMSINV($probability));
        }

        return Functions::VALUE();
    }

    /**
     * LOGNORMDIST.
     *
     * Returns the cumulative lognormal distribution of x, where ln(x) is normally distributed
     * with parameters mean and standard_dev.
     *
     * @param float $value
     * @param float $mean
     * @param float $stdDev
     *
     * @return float|string The result, or a string containing an error
     */
    public static function LOGNORMDIST($value, $mean, $stdDev)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($value <= 0) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            return self::NORMSDIST((log($value) - $mean) / $stdDev);
        }

        return Functions::VALUE();
    }

    /**
     * LOGNORM.DIST.
     *
     * Returns the lognormal distribution of x, where ln(x) is normally distributed
     * with parameters mean and standard_dev.
     *
     * @param float $value
     * @param float $mean
     * @param float $stdDev
     * @param bool $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function LOGNORMDIST2($value, $mean, $stdDev, $cumulative = false)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $cumulative = (bool) Functions::flattenSingleValue($cumulative);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($value <= 0) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            if ($cumulative === true) {
                return self::NORMSDIST2((log($value) - $mean) / $stdDev, true);
            }

            return (1 / (sqrt(2 * M_PI) * $stdDev * $value)) *
                exp(0 - ((log($value) - $mean) ** 2 / (2 * $stdDev ** 2)));
        }

        return Functions::VALUE();
    }

    /**
     * MAX.
     *
     * MAX returns the value of the element of the values passed that has the highest value,
     *        with negative numbers considered smaller than positive numbers.
     *
     * Excel Function:
     *        MAX(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Maximum::MAX()
     *      Use the MAX() method in the Statistical\Maximum class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function MAX(...$args)
    {
        return Maximum::MAX(...$args);
    }

    /**
     * MAXA.
     *
     * Returns the greatest value in a list of arguments, including numbers, text, and logical values
     *
     * Excel Function:
     *        MAXA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Maximum::MAXA()
     *      Use the MAXA() method in the Statistical\Maximum class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function MAXA(...$args)
    {
        return Maximum::MAXA(...$args);
    }

    /**
     * MAXIFS.
     *
     * Counts the maximum value within a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        MAXIFS(max_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::MAXIFS()
     *      Use the MAXIFS() method in the Statistical\Conditional class instead
     *
     * @param mixed $args Data range and criterias
     *
     * @return float
     */
    public static function MAXIFS(...$args)
    {
        return Conditional::MAXIFS(...$args);
    }

    /**
     * MEDIAN.
     *
     * Returns the median of the given numbers. The median is the number in the middle of a set of numbers.
     *
     * Excel Function:
     *        MEDIAN(value1[,value2[, ...]])
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Averages::median()
     *      Use the median() method in the Statistical\Averages class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MEDIAN(...$args)
    {
        return Statistical\Averages::median(...$args);
    }

    /**
     * MIN.
     *
     * MIN returns the value of the element of the values passed that has the smallest value,
     *        with negative numbers considered smaller than positive numbers.
     *
     * Excel Function:
     *        MIN(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Minimum::MIN()
     *      Use the MIN() method in the Statistical\Minimum class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function MIN(...$args)
    {
        return Minimum::MIN(...$args);
    }

    /**
     * MINA.
     *
     * Returns the smallest value in a list of arguments, including numbers, text, and logical values
     *
     * Excel Function:
     *        MINA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Minimum::MINA()
     *      Use the MINA() method in the Statistical\Minimum class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function MINA(...$args)
    {
        return Minimum::MINA(...$args);
    }

    /**
     * MINIFS.
     *
     * Returns the minimum value within a range of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        MINIFS(min_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::MINIFS()
     *      Use the MINIFS() method in the Statistical\Conditional class instead
     *
     * @param mixed $args Data range and criterias
     *
     * @return float
     */
    public static function MINIFS(...$args)
    {
        return Conditional::MINIFS(...$args);
    }

    /**
     * MODE.
     *
     * Returns the most frequently occurring, or repetitive, value in an array or range of data
     *
     * Excel Function:
     *        MODE(value1[,value2[, ...]])
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Averages::mode()
     *      Use the mode() method in the Statistical\Averages class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MODE(...$args)
    {
        return Statistical\Averages::mode(...$args);
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
     */
    public static function NEGBINOMDIST($failures, $successes, $probability)
    {
        $failures = floor(Functions::flattenSingleValue($failures));
        $successes = floor(Functions::flattenSingleValue($successes));
        $probability = Functions::flattenSingleValue($probability);

        if ((is_numeric($failures)) && (is_numeric($successes)) && (is_numeric($probability))) {
            if (($failures < 0) || ($successes < 1)) {
                return Functions::NAN();
            } elseif (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                if (($failures + $successes - 1) <= 0) {
                    return Functions::NAN();
                }
            }

            return (MathTrig::COMBIN($failures + $successes - 1, $successes - 1)) * ($probability ** $successes) * ((1 - $probability) ** $failures);
        }

        return Functions::VALUE();
    }

    /**
     * NORMDIST.
     *
     * Returns the normal distribution for the specified mean and standard deviation. This
     * function has a very wide range of applications in statistics, including hypothesis
     * testing.
     *
     * @param mixed (float) $value
     * @param mixed (float) $mean Mean Value
     * @param mixed (float) $stdDev Standard Deviation
     * @param mixed (bool) $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function NORMDIST($value, $mean, $stdDev, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if ($stdDev < 0) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return 0.5 * (1 + Engineering\Erf::erfValue(($value - $mean) / ($stdDev * sqrt(2))));
                }

                return (1 / (self::SQRT2PI * $stdDev)) * exp(0 - (($value - $mean) ** 2 / (2 * ($stdDev * $stdDev))));
            }
        }

        return Functions::VALUE();
    }

    /**
     * NORMINV.
     *
     * Returns the inverse of the normal cumulative distribution for the specified mean and standard deviation.
     *
     * @param mixed (float) $probability
     * @param mixed (float) $mean Mean Value
     * @param mixed (float) $stdDev Standard Deviation
     *
     * @return float|string The result, or a string containing an error
     */
    public static function NORMINV($probability, $mean, $stdDev)
    {
        $probability = Functions::flattenSingleValue($probability);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if ($stdDev < 0) {
                return Functions::NAN();
            }

            return (self::inverseNcdf($probability) * $stdDev) + $mean;
        }

        return Functions::VALUE();
    }

    /**
     * NORMSDIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed (float) $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function NORMSDIST($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }

        return self::NORMDIST($value, 0, 1, true);
    }

    /**
     * NORM.S.DIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed (float) $value
     * @param mixed (bool) $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function NORMSDIST2($value, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }
        $cumulative = (bool) Functions::flattenSingleValue($cumulative);

        return self::NORMDIST($value, 0, 1, $cumulative);
    }

    /**
     * NORMSINV.
     *
     * Returns the inverse of the standard normal cumulative distribution
     *
     * @param mixed (float) $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function NORMSINV($value)
    {
        return self::NORMINV($value, 0, 1);
    }

    /**
     * PERCENTILE.
     *
     * Returns the nth percentile of values in a range..
     *
     * Excel Function:
     *        PERCENTILE(value1[,value2[, ...]],entry)
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Percentiles::PERCENTILE()
     * Use the PERCENTILE() method in the Statistical\Percentiles class instead
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function PERCENTILE(...$args)
    {
        return Statistical\Percentiles::PERCENTILE(...$args);
    }

    /**
     * PERCENTRANK.
     *
     * Returns the rank of a value in a data set as a percentage of the data set.
     * Note that the returned rank is simply rounded to the appropriate significant digits,
     *      rather than floored (as MS Excel), so value 3 for a value set of  1, 2, 3, 4 will return
     *      0.667 rather than 0.666
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Percentiles::PERCENTRANK()
     * Use the PERCENTRANK() method in the Statistical\Percentiles class instead
     *
     * @param mixed (float[]) $valueSet An array of, or a reference to, a list of numbers
     * @param mixed (int) $value the number whose rank you want to find
     * @param mixed (int) $significance the number of significant digits for the returned percentage value
     *
     * @return float|string (string if result is an error)
     */
    public static function PERCENTRANK($valueSet, $value, $significance = 3)
    {
        return Statistical\Percentiles::PERCENTRANK($valueSet, $value, $significance);
    }

    /**
     * PERMUT.
     *
     * Returns the number of permutations for a given number of objects that can be
     *        selected from number objects. A permutation is any set or subset of objects or
     *        events where internal order is significant. Permutations are different from
     *        combinations, for which the internal order is not significant. Use this function
     *        for lottery-style probability calculations.
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Permutations::PERMUT()
     * Use the PERMUT() method in the Statistical\Permutations class instead
     *
     * @param int $numObjs Number of different objects
     * @param int $numInSet Number of objects in each permutation
     *
     * @return int|string Number of permutations, or a string containing an error
     */
    public static function PERMUT($numObjs, $numInSet)
    {
        return Permutations::PERMUT($numObjs, $numInSet);
    }

    /**
     * POISSON.
     *
     * Returns the Poisson distribution. A common application of the Poisson distribution
     * is predicting the number of events over a specific time, such as the number of
     * cars arriving at a toll plaza in 1 minute.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Poisson::distribution()
     * Use the distribution() method in the Statistical\Distributions\Poisson class instead
     *
     * @param mixed (float) $value
     * @param mixed (float) $mean Mean Value
     * @param mixed (bool) $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function POISSON($value, $mean, $cumulative)
    {
        return Statistical\Distributions\Poisson::distribution($value, $mean, $cumulative);
    }

    /**
     * QUARTILE.
     *
     * Returns the quartile of a data set.
     *
     * Excel Function:
     *        QUARTILE(value1[,value2[, ...]],entry)
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Percentiles::QUARTILE()
     * Use the QUARTILE() method in the Statistical\Percentiles class instead
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function QUARTILE(...$args)
    {
        return Statistical\Percentiles::QUARTILE(...$args);
    }

    /**
     * RANK.
     *
     * Returns the rank of a number in a list of numbers.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Percentiles::RANK()
     * Use the RANK() method in the Statistical\Percentiles class instead
     *
     * @param mixed (float) $value the number whose rank you want to find
     * @param mixed (float[]) $valueSet An array of, or a reference to, a list of numbers
     * @param mixed (int) $order Order to sort the values in the value set
     *
     * @return float|string The result, or a string containing an error
     */
    public static function RANK($value, $valueSet, $order = 0)
    {
        return Statistical\Percentiles::RANK($value, $valueSet, $order);
    }

    /**
     * RSQ.
     *
     * Returns the square of the Pearson product moment correlation coefficient through data points in known_y's and known_x's.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::RSQ()
     *      Use the RSQ() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string The result, or a string containing an error
     */
    public static function RSQ($yValues, $xValues)
    {
        return Trends::RSQ($yValues, $xValues);
    }

    /**
     * SKEW.
     *
     * Returns the skewness of a distribution. Skewness characterizes the degree of asymmetry
     * of a distribution around its mean. Positive skewness indicates a distribution with an
     * asymmetric tail extending toward more positive values. Negative skewness indicates a
     * distribution with an asymmetric tail extending toward more negative values.
     *
     * @param array ...$args Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SKEW(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = Averages::average($aArgs);
        $stdDev = StandardDeviations::STDEV($aArgs);

        if ($stdDev === 0.0 || is_string($stdDev)) {
            return Functions::DIV0();
        }

        $count = $summer = 0;
        // Loop through arguments
        foreach ($aArgs as $k => $arg) {
            if ((is_bool($arg)) && (!Functions::isMatrixValue($k))) {
            } elseif (!is_numeric($arg)) {
                return Functions::VALUE();
            } else {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $summer += (($arg - $mean) / $stdDev) ** 3;
                    ++$count;
                }
            }
        }

        if ($count > 2) {
            return $summer * ($count / (($count - 1) * ($count - 2)));
        }

        return Functions::DIV0();
    }

    /**
     * SLOPE.
     *
     * Returns the slope of the linear regression line through data points in known_y's and known_x's.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::SLOPE()
     *      Use the SLOPE() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SLOPE($yValues, $xValues)
    {
        return Trends::SLOPE($yValues, $xValues);
    }

    /**
     * SMALL.
     *
     * Returns the nth smallest value in a data set. You can use this function to
     *        select a value based on its relative standing.
     *
     * Excel Function:
     *        SMALL(value1[,value2[, ...]],entry)
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SMALL(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $entry = array_pop($aArgs);

        if ((is_numeric($entry)) && (!is_string($entry))) {
            $entry = (int) floor($entry);

            $mArgs = [];
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $count = Counts::COUNT($mArgs);
            --$entry;
            if (($entry < 0) || ($entry >= $count) || ($count == 0)) {
                return Functions::NAN();
            }
            sort($mArgs);

            return $mArgs[$entry];
        }

        return Functions::VALUE();
    }

    /**
     * STANDARDIZE.
     *
     * Returns a normalized value from a distribution characterized by mean and standard_dev.
     *
     * @param float $value Value to normalize
     * @param float $mean Mean Value
     * @param float $stdDev Standard Deviation
     *
     * @return float|string Standardized value, or a string containing an error
     */
    public static function STANDARDIZE($value, $mean, $stdDev)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if ($stdDev <= 0) {
                return Functions::NAN();
            }

            return ($value - $mean) / $stdDev;
        }

        return Functions::VALUE();
    }

    /**
     * STDEV.
     *
     * Estimates standard deviation based on a sample. The standard deviation is a measure of how
     *        widely values are dispersed from the average value (the mean).
     *
     * Excel Function:
     *        STDEV(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\StandardDeviations::STDEV()
     *      Use the STDEV() method in the Statistical\StandardDeviations class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function STDEV(...$args)
    {
        return StandardDeviations::STDEV(...$args);
    }

    /**
     * STDEVA.
     *
     * Estimates standard deviation based on a sample, including numbers, text, and logical values
     *
     * Excel Function:
     *        STDEVA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\StandardDeviations::STDEVA()
     *      Use the STDEVA() method in the Statistical\StandardDeviations class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVA(...$args)
    {
        return StandardDeviations::STDEVA(...$args);
    }

    /**
     * STDEVP.
     *
     * Calculates standard deviation based on the entire population
     *
     * Excel Function:
     *        STDEVP(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\StandardDeviations::STDEVP()
     *      Use the STDEVP() method in the Statistical\StandardDeviations class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVP(...$args)
    {
        return StandardDeviations::STDEVP(...$args);
    }

    /**
     * STDEVPA.
     *
     * Calculates standard deviation based on the entire population, including numbers, text, and logical values
     *
     * Excel Function:
     *        STDEVPA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\StandardDeviations::STDEVPA()
     *      Use the STDEVPA() method in the Statistical\StandardDeviations class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVPA(...$args)
    {
        return StandardDeviations::STDEVPA(...$args);
    }

    /**
     * STEYX.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::STEYX()
     *      Use the STEYX() method in the Statistical\Trends class instead
     *
     * Returns the standard error of the predicted y-value for each x in the regression.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string
     */
    public static function STEYX($yValues, $xValues)
    {
        return Trends::STEYX($yValues, $xValues);
    }

    /**
     * TDIST.
     *
     * Returns the probability of Student's T distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\StudentT::distribution()
     *      Use the distribution() method in the Statistical\Distributions\StudentT class instead
     *
     * @param float $value Value for the function
     * @param float $degrees degrees of freedom
     * @param float $tails number of tails (1 or 2)
     *
     * @return float|string The result, or a string containing an error
     */
    public static function TDIST($value, $degrees, $tails)
    {
        return Statistical\Distributions\StudentT::distribution($value, $degrees, $tails);
    }

    /**
     * TINV.
     *
     * Returns the one-tailed probability of the Student-T distribution.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\StudentT::inverse()
     *      Use the inverse() method in the Statistical\Distributions\StudentT class instead
     *
     * @param float $probability Probability for the function
     * @param float $degrees degrees of freedom
     *
     * @return float|string The result, or a string containing an error
     */
    public static function TINV($probability, $degrees)
    {
        return Statistical\Distributions\StudentT::inverse($probability, $degrees);
    }

    /**
     * TREND.
     *
     * Returns values along a linear Trend
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Trends::TREND()
     *      Use the TREND() method in the Statistical\Trends class instead
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     * @param mixed[] $newValues Values of X for which we want to find Y
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     *
     * @return array of float
     */
    public static function TREND($yValues, $xValues = [], $newValues = [], $const = true)
    {
        return Trends::TREND($yValues, $xValues, $newValues, $const);
    }

    /**
     * TRIMMEAN.
     *
     * Returns the mean of the interior of a data set. TRIMMEAN calculates the mean
     *        taken by excluding a percentage of data points from the top and bottom tails
     *        of a data set.
     *
     * Excel Function:
     *        TRIMEAN(value1[,value2[, ...]], $discard)
     *
     * @param mixed $args Data values
     *
     * @return float|string
     */
    public static function TRIMMEAN(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $percent = array_pop($aArgs);

        if ((is_numeric($percent)) && (!is_string($percent))) {
            if (($percent < 0) || ($percent > 1)) {
                return Functions::NAN();
            }

            $mArgs = [];
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }

            $discard = floor(Counts::COUNT($mArgs) * $percent / 2);
            sort($mArgs);

            for ($i = 0; $i < $discard; ++$i) {
                array_pop($mArgs);
                array_shift($mArgs);
            }

            return Averages::average($mArgs);
        }

        return Functions::VALUE();
    }

    /**
     * VARFunc.
     *
     * Estimates variance based on a sample.
     *
     * Excel Function:
     *        VAR(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @param mixed ...$args Data values
     *
     * @return float|string (string if result is an error)
     *
     *@see Statistical\Variances::VAR()
     *      Use the VAR() method in the Statistical\Variances class instead
     */
    public static function VARFunc(...$args)
    {
        return Variances::VAR(...$args);
    }

    /**
     * VARA.
     *
     * Estimates variance based on a sample, including numbers, text, and logical values
     *
     * Excel Function:
     *        VARA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Variances::VARA()
     *      Use the VARA() method in the Statistical\Variances class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string (string if result is an error)
     */
    public static function VARA(...$args)
    {
        return Variances::VARA(...$args);
    }

    /**
     * VARP.
     *
     * Calculates variance based on the entire population
     *
     * Excel Function:
     *        VARP(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Variances::VARP()
     *      Use the VARP() method in the Statistical\Variances class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string (string if result is an error)
     */
    public static function VARP(...$args)
    {
        return Variances::VARP(...$args);
    }

    /**
     * VARPA.
     *
     * Calculates variance based on the entire population, including numbers, text, and logical values
     *
     * Excel Function:
     *        VARPA(value1[,value2[, ...]])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Variances::VARPA()
     *      Use the VARPA() method in the Statistical\Variances class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string (string if result is an error)
     */
    public static function VARPA(...$args)
    {
        return Variances::VARPA(...$args);
    }

    /**
     * WEIBULL.
     *
     * Returns the Weibull distribution. Use this distribution in reliability
     * analysis, such as calculating a device's mean time to failure.
     *
     * @Deprecated 1.18.0
     *
     * @see Statistical\Distributions\Weibull::distribution()
     *      Use the distribution() method in the Statistical\Distributions\Weibull class instead
     *
     * @param float $value
     * @param float $alpha Alpha Parameter
     * @param float $beta Beta Parameter
     * @param bool $cumulative
     *
     * @return float|string (string if result is an error)
     */
    public static function WEIBULL($value, $alpha, $beta, $cumulative)
    {
        return Statistical\Distributions\Weibull::distribution($value, $alpha, $beta, $cumulative);
    }

    /**
     * ZTEST.
     *
     * Returns the Weibull distribution. Use this distribution in reliability
     * analysis, such as calculating a device's mean time to failure.
     *
     * @param float $dataSet
     * @param float $m0 Alpha Parameter
     * @param float $sigma Beta Parameter
     *
     * @return float|string (string if result is an error)
     */
    public static function ZTEST($dataSet, $m0, $sigma = null)
    {
        $dataSet = Functions::flattenArrayIndexed($dataSet);
        $m0 = Functions::flattenSingleValue($m0);
        $sigma = Functions::flattenSingleValue($sigma);

        if ($sigma === null) {
            $sigma = StandardDeviations::STDEV($dataSet);
        }
        $n = count($dataSet);

        return 1 - self::NORMSDIST((Averages::average($dataSet) - $m0) / ($sigma / sqrt($n)));
    }
}
