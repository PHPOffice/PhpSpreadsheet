<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Payments
{
    /**
     * PMT.
     *
     * Returns the constant payment (annuity) for a cash flow with a constant interest rate.
     *
     * @param float $interestRate Interest rate per period
     * @param int $numberOfPeriods Number of periods
     * @param float $presentValue Present Value
     * @param float $futureValue Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PMT($interestRate = 0, $numberOfPeriods = 0, $presentValue = 0, $futureValue = 0, $type = 0)
    {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue);
        $type = Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($interestRate !== null && $interestRate != 0) {
            return (-$futureValue - $presentValue * (1 + $interestRate) ** $numberOfPeriods) /
                (1 + $interestRate * $type) / (((1 + $interestRate) ** $numberOfPeriods - 1) / $interestRate);
        }

        return (-$presentValue - $futureValue) / $numberOfPeriods;
    }

    /**
     * PPMT.
     *
     * Returns the interest payment for a given period for an investment based on periodic, constant payments
     *         and a constant interest rate.
     *
     * @param float $interestRate Interest rate per period
     * @param int $period Period for which we want to find the interest
     * @param int $numberOfPeriods Number of periods
     * @param float $presentValue Present Value
     * @param float $futureValue Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PPMT($interestRate, $period, $numberOfPeriods, $presentValue, $futureValue = 0, $type = 0)
    {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $period = (int) Functions::flattenSingleValue($period);
        $numberOfPeriods = (int) Functions::flattenSingleValue($numberOfPeriods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue);
        $type = (int) Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($period <= 0 || $period > $numberOfPeriods) {
            return Functions::NAN();
        }

        // Calculate
        $interestAndPrincipal = new InterestAndPrincipal(
            $interestRate,
            $period,
            $numberOfPeriods,
            $presentValue,
            $futureValue,
            $type
        );

        return $interestAndPrincipal->principal();
    }
}
