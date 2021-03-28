<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ChiSquared
{
    use BaseValidations;

    private const MAX_ITERATIONS = 256;

    /**
     * CHIDIST.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed (float) $value Value for the function
     * @param mixed (int) $degrees degrees of freedom
     *
     * @return float|string
     */
    public static function distributionRightTail($value, $degrees)
    {
        $value = Functions::flattenSingleValue($value);
        $degrees = Functions::flattenSingleValue($degrees);

        try {
            $value = self::validateFloat($value);
            $degrees = self::validateInt($degrees);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($degrees < 1) {
            return Functions::NAN();
        }
        if ($value < 0) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return 1;
            }

            return Functions::NAN();
        }

        return 1 - (Gamma::incompleteGamma($degrees / 2, $value / 2) / Gamma::gammaValue($degrees / 2));
    }

    /**
     * CHIDIST.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed (float) $value Value for the function
     * @param mixed (int) $degrees degrees of freedom
     * @param mixed $cumulative
     *
     * @return float|string
     */
    public static function distributionLeftTail($value, $degrees, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $degrees = Functions::flattenSingleValue($degrees);
        $cumulative = Functions::flattenSingleValue($cumulative);

        try {
            $value = self::validateFloat($value);
            $degrees = self::validateInt($degrees);
            $cumulative = self::validateBool($cumulative);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($degrees < 1) {
            return Functions::NAN();
        }
        if ($value < 0) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return 1;
            }

            return Functions::NAN();
        }

        if ($cumulative === true) {
            return 1 - self::distributionRightTail($value, $degrees);
        }

        return (($value ** (($degrees / 2) - 1) * exp(-$value / 2))) /
            ((2 ** ($degrees / 2)) * Gamma::gammaValue($degrees / 2));
    }

    /**
     * CHIINV.
     *
     * Returns the inverse of the right-tailed probability of the chi-squared distribution.
     *
     * @param mixed (float) $probability Probability for the function
     * @param mixed (int) $degrees degrees of freedom
     *
     * @return float|string
     */
    public static function inverseRightTail($probability, $degrees)
    {
        $probability = Functions::flattenSingleValue($probability);
        $degrees = Functions::flattenSingleValue($degrees);

        try {
            $probability = self::validateFloat($probability);
            $degrees = self::validateInt($degrees);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($probability < 0.0 || $probability > 1.0 || $degrees < 1) {
            return Functions::NAN();
        }

        $callback = function ($value) use ($degrees) {
            return 1 - (Gamma::incompleteGamma($degrees / 2, $value / 2)
                    / Gamma::gammaValue($degrees / 2));
        };

        $newtonRaphson = new NewtonRaphson($callback);

        return $newtonRaphson->execute($probability);
    }

    public static function test($actual, $expected)
    {
        $rows = count($actual);
        $actual = Functions::flattenArray($actual);
        $expected = Functions::flattenArray($expected);
        $columns = count($actual) / $rows;

        $countActuals = count($actual);
        $countExpected = count($expected);
        if ($countActuals !== $countExpected || $countActuals === 1) {
            return Functions::NAN();
        }

        $result = 0.0;
        for ($i = 0; $i < $countActuals; ++$i) {
            if ($expected[$i] == 0.0) {
                return Functions::DIV0();
            } elseif ($expected[$i] < 0.0) {
                return Functions::NAN();
            }
            $result += (($actual[$i] - $expected[$i]) ** 2) / $expected[$i];
        }

        $degrees = self::degrees($rows, $columns);

        $result = self::distributionRightTail($result, $degrees);

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
}
