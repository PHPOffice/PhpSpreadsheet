<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Amortization
{
    /**
     * AMORDEGRC.
     *
     * Returns the depreciation for each accounting period.
     * This function is provided for the French accounting system. If an asset is purchased in
     * the middle of the accounting period, the prorated depreciation is taken into account.
     * The function is similar to AMORLINC, except that a depreciation coefficient is applied in
     * the calculation depending on the life of the assets.
     * This function will return the depreciation until the last period of the life of the assets
     * or until the cumulated value of depreciation is greater than the cost of the assets minus
     * the salvage value.
     *
     * Excel Function:
     *        AMORDEGRC(cost,purchased,firstPeriod,salvage,period,rate[,basis])
     *
     * @param mixed $cost The float cost of the asset
     * @param mixed $purchased Date of the purchase of the asset
     * @param mixed $firstPeriod Date of the end of the first period
     * @param mixed $salvage The salvage value at the end of the life of the asset
     * @param mixed $period the period (float)
     * @param mixed $rate rate of depreciation (float)
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return float|string (string containing the error type if there is an error)
     */
    public static function AMORDEGRC(
        mixed $cost,
        mixed $purchased,
        mixed $firstPeriod,
        mixed $salvage,
        mixed $period,
        mixed $rate,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ): string|float {
        $cost = Functions::flattenSingleValue($cost);
        $purchased = Functions::flattenSingleValue($purchased);
        $firstPeriod = Functions::flattenSingleValue($firstPeriod);
        $salvage = Functions::flattenSingleValue($salvage);
        $period = Functions::flattenSingleValue($period);
        $rate = Functions::flattenSingleValue($rate);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $cost = FinancialValidations::validateFloat($cost);
            $purchased = FinancialValidations::validateDate($purchased);
            $firstPeriod = FinancialValidations::validateDate($firstPeriod);
            $salvage = FinancialValidations::validateFloat($salvage);
            $period = FinancialValidations::validateInt($period);
            $rate = FinancialValidations::validateFloat($rate);
            $basis = FinancialValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $yearFracx = DateTimeExcel\YearFrac::fraction($purchased, $firstPeriod, $basis);
        if (is_string($yearFracx)) {
            return $yearFracx;
        }
        /** @var float $yearFrac */
        $yearFrac = $yearFracx;

        $amortiseCoeff = self::getAmortizationCoefficient($rate);

        $rate *= $amortiseCoeff;
        $rate = (float) (string) $rate; // ugly way to avoid rounding problem
        $fNRate = round($yearFrac * $rate * $cost, 0);
        $cost -= $fNRate;
        $fRest = $cost - $salvage;

        for ($n = 0; $n < $period; ++$n) {
            $fNRate = round($rate * $cost, 0);
            $fRest -= $fNRate;

            if ($fRest < 0.0) {
                return match ($period - $n) {
                    1 => round($cost * 0.5, 0),
                    default => 0.0,
                };
            }
            $cost -= $fNRate;
        }

        return $fNRate;
    }

    /**
     * AMORLINC.
     *
     * Returns the depreciation for each accounting period.
     * This function is provided for the French accounting system. If an asset is purchased in
     * the middle of the accounting period, the prorated depreciation is taken into account.
     *
     * Excel Function:
     *        AMORLINC(cost,purchased,firstPeriod,salvage,period,rate[,basis])
     *
     * @param mixed $cost The cost of the asset as a float
     * @param mixed $purchased Date of the purchase of the asset
     * @param mixed $firstPeriod Date of the end of the first period
     * @param mixed $salvage The salvage value at the end of the life of the asset
     * @param mixed $period The period as a float
     * @param mixed $rate Rate of depreciation as  float
     * @param mixed $basis Integer indicating the type of day count to use.
     *                             0 or omitted    US (NASD) 30/360
     *                             1               Actual/actual
     *                             2               Actual/360
     *                             3               Actual/365
     *                             4               European 30/360
     *
     * @return float|string (string containing the error type if there is an error)
     */
    public static function AMORLINC(
        mixed $cost,
        mixed $purchased,
        mixed $firstPeriod,
        mixed $salvage,
        mixed $period,
        mixed $rate,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ): string|float {
        $cost = Functions::flattenSingleValue($cost);
        $purchased = Functions::flattenSingleValue($purchased);
        $firstPeriod = Functions::flattenSingleValue($firstPeriod);
        $salvage = Functions::flattenSingleValue($salvage);
        $period = Functions::flattenSingleValue($period);
        $rate = Functions::flattenSingleValue($rate);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $cost = FinancialValidations::validateFloat($cost);
            $purchased = FinancialValidations::validateDate($purchased);
            $firstPeriod = FinancialValidations::validateDate($firstPeriod);
            $salvage = FinancialValidations::validateFloat($salvage);
            $period = FinancialValidations::validateFloat($period);
            $rate = FinancialValidations::validateFloat($rate);
            $basis = FinancialValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $fOneRate = $cost * $rate;
        $fCostDelta = $cost - $salvage;
        //    Note, quirky variation for leap years on the YEARFRAC for this function
        $purchasedYear = DateTimeExcel\DateParts::year($purchased);
        $yearFracx = DateTimeExcel\YearFrac::fraction($purchased, $firstPeriod, $basis);
        if (is_string($yearFracx)) {
            return $yearFracx;
        }
        /** @var float $yearFrac */
        $yearFrac = $yearFracx;

        if (
            $basis == FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL
            && $yearFrac < 1
        ) {
            $temp = Functions::scalar($purchasedYear);
            if (is_int($temp) || is_string($temp)) {
                if (DateTimeExcel\Helpers::isLeapYear($temp)) {
                    $yearFrac *= 365 / 366;
                }
            }
        }

        $f0Rate = $yearFrac * $rate * $cost;
        $nNumOfFullPeriods = (int) (($cost - $salvage - $f0Rate) / $fOneRate);

        if ($period == 0) {
            return $f0Rate;
        } elseif ($period <= $nNumOfFullPeriods) {
            return $fOneRate;
        } elseif ($period == ($nNumOfFullPeriods + 1)) {
            return $fCostDelta - $fOneRate * $nNumOfFullPeriods - $f0Rate;
        }

        return 0.0;
    }

    private static function getAmortizationCoefficient(float $rate): float
    {
        //    The depreciation coefficients are:
        //    Life of assets (1/rate)        Depreciation coefficient
        //    Less than 3 years            1
        //    Between 3 and 4 years        1.5
        //    Between 5 and 6 years        2
        //    More than 6 years            2.5
        $fUsePer = 1.0 / $rate;

        if ($fUsePer < 3.0) {
            return 1.0;
        } elseif ($fUsePer < 4.0) {
            return 1.5;
        } elseif ($fUsePer <= 6.0) {
            return 2.0;
        }

        return 2.5;
    }
}
