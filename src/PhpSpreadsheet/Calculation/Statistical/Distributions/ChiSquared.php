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
    public static function distribution($value, $degrees)
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
     * CHIINV.
     *
     * Returns the one-tailed probability of the chi-squared distribution.
     *
     * @param mixed (float) $probability Probability for the function
     * @param mixed (int) $degrees degrees of freedom
     *
     * @return float|string
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

        if ($probability < 0.0 || $probability > 1.0 || $degrees < 1) {
            return Functions::NAN();
        }

        return self::calculateInverse($degrees, $probability);
    }

    /**
     * @return float|string
     */
    protected static function calculateInverse(int $degrees, float $probability)
    {
        $xLo = 100;
        $xHi = 0;

        $x = $xNew = 1;
        $dx = 1;
        $i = 0;

        while ((abs($dx) > Functions::PRECISION) && (++$i <= self::MAX_ITERATIONS)) {
            // Apply Newton-Raphson step
            $result = 1 - (Gamma::incompleteGamma($degrees / 2, $x / 2)
                    / Gamma::gammaValue($degrees / 2));
            $error = $result - $probability;

            if ($error == 0.0) {
                $dx = 0;
            } elseif ($error < 0.0) {
                $xLo = $x;
            } else {
                $xHi = $x;
            }

            // Avoid division by zero
            if ($result != 0.0) {
                $dx = $error / $result;
                $xNew = $x - $dx;
            }

            // If the NR fails to converge (which for example may be the
            // case if the initial guess is too rough) we apply a bisection
            // step to determine a more narrow interval around the root.
            if (($xNew < $xLo) || ($xNew > $xHi) || ($result == 0.0)) {
                $xNew = ($xLo + $xHi) / 2;
                $dx = $xNew - $x;
            }
            $x = $xNew;
        }

        if ($i === self::MAX_ITERATIONS) {
            return Functions::NA();
        }

        return $x;
    }
}
