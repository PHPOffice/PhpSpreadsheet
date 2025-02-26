<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class TreasuryBill
{
    /**
     * TBILLEQ.
     *
     * Returns the bond-equivalent yield for a Treasury bill.
     *
     * @param mixed $settlement The Treasury bill's settlement date.
     *                                The Treasury bill's settlement date is the date after the issue date
     *                                    when the Treasury bill is traded to the buyer.
     * @param mixed $maturity The Treasury bill's maturity date.
     *                                The maturity date is the date when the Treasury bill expires.
     * @param mixed $discount The Treasury bill's discount rate
     *
     * @return float|string Result, or a string containing an error
     */
    public static function bondEquivalentYield(mixed $settlement, mixed $maturity, mixed $discount): string|float
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            $discount = FinancialValidations::validateFloat($discount);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($discount <= 0) {
            return ExcelError::NAN();
        }

        $daysBetweenSettlementAndMaturity = $maturity - $settlement;
        $daysPerYear = Helpers::daysPerYear(
            Functions::scalar(DateTimeExcel\DateParts::year($maturity)),
            FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL
        );

        if ($daysBetweenSettlementAndMaturity > $daysPerYear || $daysBetweenSettlementAndMaturity < 0) {
            return ExcelError::NAN();
        }

        return (365 * $discount) / (360 - $discount * $daysBetweenSettlementAndMaturity);
    }

    /**
     * TBILLPRICE.
     *
     * Returns the price per $100 face value for a Treasury bill.
     *
     * @param mixed $settlement The Treasury bill's settlement date.
     *                                The Treasury bill's settlement date is the date after the issue date
     *                                    when the Treasury bill is traded to the buyer.
     * @param mixed $maturity The Treasury bill's maturity date.
     *                                The maturity date is the date when the Treasury bill expires.
     * @param mixed $discount The Treasury bill's discount rate
     *
     * @return float|string Result, or a string containing an error
     */
    public static function price(mixed $settlement, mixed $maturity, mixed $discount): string|float
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            $discount = FinancialValidations::validateFloat($discount);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($discount <= 0) {
            return ExcelError::NAN();
        }

        $daysBetweenSettlementAndMaturity = $maturity - $settlement;
        $daysPerYear = Helpers::daysPerYear(
            Functions::scalar(DateTimeExcel\DateParts::year($maturity)),
            FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL
        );

        if ($daysBetweenSettlementAndMaturity > $daysPerYear || $daysBetweenSettlementAndMaturity < 0) {
            return ExcelError::NAN();
        }

        $price = 100 * (1 - (($discount * $daysBetweenSettlementAndMaturity) / 360));
        if ($price < 0.0) {
            return ExcelError::NAN();
        }

        return $price;
    }

    /**
     * TBILLYIELD.
     *
     * Returns the yield for a Treasury bill.
     *
     * @param mixed $settlement The Treasury bill's settlement date.
     *                                The Treasury bill's settlement date is the date after the issue date when
     *                                    the Treasury bill is traded to the buyer.
     * @param mixed $maturity The Treasury bill's maturity date.
     *                                The maturity date is the date when the Treasury bill expires.
     * @param float|string $price The Treasury bill's price per $100 face value
     */
    public static function yield(mixed $settlement, mixed $maturity, $price): string|float
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            $price = FinancialValidations::validatePrice($price);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysBetweenSettlementAndMaturity = $maturity - $settlement;
        $daysPerYear = Helpers::daysPerYear(
            Functions::scalar(DateTimeExcel\DateParts::year($maturity)),
            FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL
        );

        if ($daysBetweenSettlementAndMaturity > $daysPerYear || $daysBetweenSettlementAndMaturity < 0) {
            return ExcelError::NAN();
        }

        return ((100 - $price) / $price) * (360 / $daysBetweenSettlementAndMaturity);
    }
}
