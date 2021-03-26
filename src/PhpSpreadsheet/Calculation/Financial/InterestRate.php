<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class InterestRate
{
    use BaseValidations;

    /**
     * EFFECT.
     *
     * Returns the effective interest rate given the nominal rate and the number of
     *        compounding payments per year.
     *
     * Excel Function:
     *        EFFECT(nominal_rate,npery)
     *
     * @param mixed (float) $nominalRate Nominal interest rate
     * @param mixed (int) $periodsPerYear Number of compounding payments per year
     *
     * @return float|string
     */
    public static function effective($nominalRate = 0, $periodsPerYear = 0)
    {
        $nominalRate = Functions::flattenSingleValue($nominalRate);
        $periodsPerYear = Functions::flattenSingleValue($periodsPerYear);

        try {
            $nominalRate = self::validateFloat($nominalRate);
            $periodsPerYear = self::validateInt($periodsPerYear);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($nominalRate <= 0 || $periodsPerYear < 1) {
            return Functions::NAN();
        }

        return ((1 + $nominalRate / $periodsPerYear) ** $periodsPerYear) - 1;
    }

    /**
     * NOMINAL.
     *
     * Returns the nominal interest rate given the effective rate and the number of compounding payments per year.
     *
     * @param mixed (float) $effectiveRate Effective interest rate
     * @param mixed (int) $periodsPerYear Number of compounding payments per year
     *
     * @return float|string Result, or a string containing an error
     */
    public static function nominal($effectiveRate = 0, $periodsPerYear = 0)
    {
        $effectiveRate = Functions::flattenSingleValue($effectiveRate);
        $periodsPerYear = Functions::flattenSingleValue($periodsPerYear);

        try {
            $effectiveRate = self::validateFloat($effectiveRate);
            $periodsPerYear = self::validateInt($periodsPerYear);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($effectiveRate <= 0 || $periodsPerYear < 1) {
            return Functions::NAN();
        }

        // Calculate
        return $periodsPerYear * (($effectiveRate + 1) ** (1 / $periodsPerYear) - 1);
    }
}
