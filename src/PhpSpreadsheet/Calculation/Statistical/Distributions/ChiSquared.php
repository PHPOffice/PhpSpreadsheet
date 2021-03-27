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

        $callback = function ($value) use ($degrees) {
            return 1 - (Gamma::incompleteGamma($degrees / 2, $value / 2)
                    / Gamma::gammaValue($degrees / 2));
        };

        $newtonRaphson = new NewtonRaphson($callback);

        return $newtonRaphson->execute($probability);
    }
}
