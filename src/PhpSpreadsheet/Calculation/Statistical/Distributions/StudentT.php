<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

/**
 * Some of this code is drived from Perl CPAN Statistical::Distributions.
 * Its copyright statement is:
 * Copyright 2003 Michael Kospach. All rights reserved.
 *
 * This library is free software; you can redistribute it and/or modify it under the same terms as Perl itself.
 */
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
        return self::calcDistribution($value, $degrees, $tails, self::distribution(...));
    }

    /**
     * T.DIST.2T.
     * Returns the two-tailed Student's t distribution.
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    public static function tDotDistDot2T(mixed $value, mixed $degrees)
    {
        return self::calcDistribution($value, $degrees, 2, self::distribution(...));
    }

    /**
     * T.DIST.RT.
     * Returns the right-tailed Student's t distribution.
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    public static function tDotDistDotRT(mixed $value, mixed $degrees)
    {
        return self::calcDistribution($value, $degrees, 1, self::distribution(...));
    }

    /**
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    private static function calcDistribution(mixed $value, mixed $degrees, mixed $tails, callable $callback)
    {
        if (is_array($value) || is_array($degrees) || is_array($tails)) {
            return self::evaluateArrayArguments($callback, $value, $degrees, $tails);
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

        return self::subTProb($value, $degrees, $tails);
    }

    /**
     * Based on code from Perl CPAN Statistical::Distributions.
     */
    private static function subTProb(float $x, int $n, int $tails): float
    {
        $w = atan2($x / sqrt($n), 1);
        $z = cos($w) ** 2;
        $y = 1;

        for ($i = $n - 2; $i >= 2; $i -= 2) {
            $y = 1 + ($i - 1) / $i * $z * $y;
        }

        if ($n % 2 == 0) {
            $a = sin($w) / 2;
            $b = 0.5;
        } else {
            $a = ($n == 1) ? 0 : (sin($w) * cos($w) / M_PI);
            $b = 0.5 + $w / M_PI;
        }

        return $tails * max(0, 1 - $b - $a * $y);
    }

    /**
     * T.DIST.
     * Returns the Student's left-tailed t distribution,
     * either as a cumulative distribution function (cdf) (TRUE)
     * or as a probability density function (pdf) (FALSE),
     * where TRUE/FALSE are the value of $cumulative parameter.
     *
     * "True" algoritm adapted from java.
     * org.apache.commons.math3.distribution.TDistribution.
     * "False" algorithm comes from:
     * https://statproofbook.github.io/P/t-pdf.html
     *
     * @param mixed $cumulative Expecting bool. See above for explanation.
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    public static function tDotDist(mixed $value, mixed $degrees, mixed $cumulative)
    {
        if (is_array($value) || is_array($degrees) || is_array($cumulative)) {
            return self::evaluateArrayArguments(self::tDotDist(...), $value, $degrees, $cumulative);
        }

        try {
            $value = DistributionValidations::validateFloat($value);
            $degrees = DistributionValidations::validateInt($degrees);
            $cumulative = DistributionValidations::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        /** @var int $degrees */
        if (($degrees < 1)) {
            return ExcelError::NAN();
        }
        /** @var float $value */
        if (!$cumulative) {
            return self::tDotDistFalse($value, $degrees);
        }

        $f16 = $degrees / ($degrees + $value * $value);
        $g16 = 0.5 * $degrees;
        $h16 = 0.5;
        $result = Beta::distribution($f16, $g16, $h16);
        if (is_numeric($result)) {
            $result = ($value < 0) ? (0.5 * $result) : (1 - 0.5 * $result);
        }

        return $result;
    }

    private static function tDotDistFalse(float $value, int $degrees): float|string
    {
        $result = $k15 = Gamma::gamma(($degrees + 1) / 2);
        if (is_numeric($k15)) {
            $result = $k16 = Gamma::gamma($degrees / 2);
            if (is_numeric($k16)) {
                $k17 = sqrt(M_PI * $degrees);
                $k18 = $k15 / ($k16 * $k17);
                $k19 = $value * $value / $degrees + 1;
                $k20 = -($degrees + 1) / 2;
                $k21 = $k19 ** $k20;
                $result = $k18 * $k21;
            }
        }

        /** @var float|string $result */
        return $result;
    }

    /**
     * TINV and T.INV.2T.
     * Returns the two-tailed inverse of the Student t distribution.
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
        return self::calcInverse($probability, $degrees, 2, self::inverse(...));
    }

    /**
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    private static function calcInverse(mixed $probability, mixed $degrees, int $tails, callable $callback2)
    {
        if (is_array($probability) || is_array($degrees)) {
            return self::evaluateArrayArguments($callback2, $probability, $degrees, $tails);
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

        $callback = fn ($value) => self::distribution($value, $degrees, $tails);

        $newtonRaphson = new NewtonRaphson($callback);

        $result = $newtonRaphson->execute($probability);
        if (is_numeric($result) && $tails === 1) {
            $result = -$result; // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * T.INV.
     * Returns the left-tailed inverse of the Student's t distribution.
     *
     * Based on code from Perl CPAN Statistical::Distributions.
     *
     * @return array<mixed>|float|string The result, or a string containing an error
     */
    public static function tDotInv(mixed $probability, mixed $degrees)
    {
        if (is_array($probability) || is_array($degrees)) {
            return self::evaluateArrayArguments(self::tDotInv(...), $probability, $degrees);
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
        if ($probability == 0.5) {
            return 0.0;
        }
        if ($probability < 0.5) {
            $result = self::tDotInv(1.0 - $probability, $degrees);

            return is_numeric($result) ? -$result : $result;
        }
        $p = $probability;
        $n = $degrees;
        $u = self::subU($p);
        $u2 = $u ** 2;

        $a = ($u2 + 1) / 4;
        $b = ((5 * $u2 + 16) * $u2 + 3) / 96;
        $c = (((3 * $u2 + 19) * $u2 + 17) * $u2 - 15) / 384;
        $d = ((((79 * $u2 + 776) * $u2 + 1482) * $u2 - 1920) * $u2 - 945) / 92160;
        $e = (((((27 * $u2 + 339) * $u2 + 930) * $u2 - 1782) * $u2 - 765) * $u2 + 17955) / 368640;

        $x = $u * (1 + ($a + ($b + ($c + ($d + $e / $n) / $n) / $n) / $n) / $n);

        if ($n <= log10($p) ** 2 + 3) {
            do {
                $p1 = self::subTProb($x, $n, 1);
                $n1 = $n + 1;
                $delta = ($p1 - $p)
                    / exp(($n1 * log($n1 / ($n + $x * $x))
                    + log($n / $n1 / 2 / M_PI) - 1
                        + (1 / $n1 - 1 / $n) / 6) / 2);
                $x += $delta;
                $round = sprintf('%.' . abs((int) (log10(abs($x)) - 4)) . 'F', $delta);
            } while (($x) && ($round != 0));
        }

        return -$x;
    }

    /**
     * Based on code from Perl CPAN Statistical::Distributions.
     */
    private static function subU(float $p): float
    {
        $y = -log(4 * $p * (1 - $p));
        $x = sqrt(
            $y * (1.570796288
              + $y * (.03706987906
                + $y * (-.8364353589E-3
                  + $y * (-.2250947176E-3
                    + $y * (.6841218299E-5
                      + $y * (0.5824238515E-5
                        + $y * (-.104527497E-5
                          + $y * (.8360937017E-7
                            + $y * (-.3231081277E-8
                              + $y * (.3657763036E-10
                                + $y * .6936233982E-12))))))))))
        );
        if ($p > 0.5) {
            $x = -$x;
        }

        return $x;
    }
}
