<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Single
{
    /**
     * FVSCHEDULE.
     *
     * Returns the future value of an initial principal after applying a series of compound interest rates.
     * Use FVSCHEDULE to calculate the future value of an investment with a variable or adjustable rate.
     *
     * Excel Function:
     *        FVSCHEDULE(principal,schedule)
     *
     * @param mixed $principal the present value
     * @param float[] $schedule an array of interest rates to apply
     */
    public static function futureValue(mixed $principal, array $schedule): string|float
    {
        $principal = Functions::flattenSingleValue($principal);
        $schedule = Functions::flattenArray($schedule);

        try {
            $principal = CashFlowValidations::validateFloat($principal);

            foreach ($schedule as $rate) {
                $rate = CashFlowValidations::validateFloat($rate);
                $principal *= 1 + $rate;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $principal;
    }

    /**
     * PDURATION.
     *
     * Calculates the number of periods required for an investment to reach a specified value.
     *
     * @param mixed $rate Interest rate per period
     * @param mixed $presentValue Present Value
     * @param mixed $futureValue Future Value
     *
     * @return float|string Result, or a string containing an error
     */
    public static function periods(mixed $rate, mixed $presentValue, mixed $futureValue): string|float
    {
        $rate = Functions::flattenSingleValue($rate);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue);

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($rate <= 0.0 || $presentValue <= 0.0 || $futureValue <= 0.0) {
            return ExcelError::NAN();
        }

        return (log($futureValue) - log($presentValue)) / log(1 + $rate);
    }

    /**
     * RRI.
     *
     * Calculates the interest rate required for an investment to grow to a specified future value .
     *
     * @param array|float $periods The number of periods over which the investment is made
     * @param array|float $presentValue Present Value
     * @param array|float $futureValue Future Value
     *
     * @return float|string Result, or a string containing an error
     */
    public static function interestRate(array|float $periods = 0.0, array|float $presentValue = 0.0, array|float $futureValue = 0.0): string|float
    {
        $periods = Functions::flattenSingleValue($periods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue);

        try {
            $periods = CashFlowValidations::validateFloat($periods);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($periods <= 0.0 || $presentValue <= 0.0 || $futureValue < 0.0) {
            return ExcelError::NAN();
        }

        return ($futureValue / $presentValue) ** (1 / $periods) - 1;
    }
}
