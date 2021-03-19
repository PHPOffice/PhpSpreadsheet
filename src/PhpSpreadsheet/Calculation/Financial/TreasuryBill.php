<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
     * @param int $discount The Treasury bill's discount rate
     *
     * @return float|string Result, or a string containing an error
     */
    public static function bondEquivalentYield($settlement, $maturity, $discount)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        if (
            is_string($maturity = DateTime::getDateValue($maturity)) ||
            is_string($settlement = DateTime::getDateValue($settlement))
        ) {
            return Functions::VALUE();
        }

        //    Validate
        if (is_numeric($discount)) {
            if ($discount <= 0) {
                return Functions::NAN();
            }

            $daysBetweenSettlementAndMaturity = $maturity - $settlement;

            if (
                $daysBetweenSettlementAndMaturity > Helpers::daysPerYear(DateTime::YEAR($maturity), Helpers::DAYS_PER_YEAR_ACTUAL) ||
                $daysBetweenSettlementAndMaturity < 0
            ) {
                return Functions::NAN();
            }

            return (365 * $discount) / (360 - $discount * $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
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
     * @param int $discount The Treasury bill's discount rate
     *
     * @return float|string Result, or a string containing an error
     */
    public static function price($settlement, $maturity, $discount)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        if (
            is_string($maturity = DateTime::getDateValue($maturity)) ||
            is_string($settlement = DateTime::getDateValue($settlement))
        ) {
            return Functions::VALUE();
        }

        //    Validate
        if (is_numeric($discount)) {
            if ($discount <= 0) {
                return Functions::NAN();
            }

            $daysBetweenSettlementAndMaturity = $maturity - $settlement;

            if (
                $daysBetweenSettlementAndMaturity > Helpers::daysPerYear(DateTime::YEAR($maturity), Helpers::DAYS_PER_YEAR_ACTUAL) ||
                $daysBetweenSettlementAndMaturity < 0
            ) {
                return Functions::NAN();
            }
            $price = 100 * (1 - (($discount * $daysBetweenSettlementAndMaturity) / 360));
            if ($price < 0.0) {
                return Functions::NAN();
            }

            return $price;
        }

        return Functions::VALUE();
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
     * @param int $price The Treasury bill's price per $100 face value
     *
     * @return float|string
     */
    public static function yield($settlement, $maturity, $price)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);

        if (
            is_string($maturity = DateTime::getDateValue($maturity)) ||
            is_string($settlement = DateTime::getDateValue($settlement))
        ) {
            return Functions::VALUE();
        }

        //    Validate
        if (is_numeric($price)) {
            if ($price <= 0) {
                return Functions::NAN();
            }

            $daysBetweenSettlementAndMaturity = $maturity - $settlement;

            if ($daysBetweenSettlementAndMaturity > 360 || $daysBetweenSettlementAndMaturity < 0) {
                return Functions::NAN();
            }

            return ((100 - $price) / $price) * (360 / $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }
}
