<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Price
{
    use BaseValidations;

    /**
     * PRICE.
     *
     * Returns the price per $100 face value of a security that pays periodic interest.
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed (float) $rate the security's annual coupon rate
     * @param mixed (float) $yield the security's annual yield
     * @param mixed (float) $redemption The number of coupon payments per year.
     *                              For annual payments, frequency = 1;
     *                              for semiannual, frequency = 2;
     *                              for quarterly, frequency = 4.
     * @param mixed (int) $frequency
     * @param mixed (int) $basis The type of day count to use.
     *                       0 or omitted    US (NASD) 30/360
     *                       1                Actual/actual
     *                       2                Actual/360
     *                       3                Actual/365
     *                       4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function price($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $rate = Functions::flattenSingleValue($rate);
        $yield = Functions::flattenSingleValue($yield);
        $redemption = Functions::flattenSingleValue($redemption);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateSecurityPeriod($settlement, $maturity);
            $rate = self::validateRate($rate);
            $yield = self::validateYield($yield);
            $redemption = self::validateRedemption($redemption);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $dsc = Coupons::COUPDAYSNC($settlement, $maturity, $frequency, $basis);
        $e = Coupons::COUPDAYS($settlement, $maturity, $frequency, $basis);
        $n = Coupons::COUPNUM($settlement, $maturity, $frequency, $basis);
        $a = Coupons::COUPDAYBS($settlement, $maturity, $frequency, $basis);

        $baseYF = 1.0 + ($yield / $frequency);
        $rfp = 100 * ($rate / $frequency);
        $de = $dsc / $e;

        $result = $redemption / $baseYF ** (--$n + $de);
        for ($k = 0; $k <= $n; ++$k) {
            $result += $rfp / ($baseYF ** ($k + $de));
        }
        $result -= $rfp * ($a / $e);

        return $result;
    }

    /**
     * PRICEDISC.
     *
     * Returns the price per $100 face value of a discounted security.
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed (float) $discount The security's discount rate
     * @param mixed (float) $redemption The security's redemption value per $100 face value
     * @param mixed (int) $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function priceDiscounted($settlement, $maturity, $discount, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateSecurityPeriod($settlement, $maturity);
            $discount = self::validateDiscount($discount);
            $redemption = self::validateRedemption($redemption);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
        if (!is_numeric($daysBetweenSettlementAndMaturity)) {
            //    return date error
            return $daysBetweenSettlementAndMaturity;
        }

        return $redemption * (1 - $discount * $daysBetweenSettlementAndMaturity);
    }

    /**
     * PRICEMAT.
     *
     * Returns the price per $100 face value of a security that pays interest at maturity.
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security's settlement date is the date after the issue date when the
     *                              security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $issue The security's issue date
     * @param mixed (float) $rate The security's interest rate at date of issue
     * @param mixed (float) $yield The security's annual yield
     * @param mixed (int) $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function priceAtMaturity($settlement, $maturity, $issue, $rate, $yield, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $issue = Functions::flattenSingleValue($issue);
        $rate = Functions::flattenSingleValue($rate);
        $yield = Functions::flattenSingleValue($yield);
        $basis = Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateSecurityPeriod($settlement, $maturity);
            $issue = self::validateIssueDate($issue);
            $rate = self::validateRate($rate);
            $yield = self::validateYield($yield);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(DateTimeExcel\Year::funcYear($settlement), $basis);
        if (!is_numeric($daysPerYear)) {
            return $daysPerYear;
        }
        $daysBetweenIssueAndSettlement = DateTimeExcel\YearFrac::funcYearFrac($issue, $settlement, $basis);
        if (!is_numeric($daysBetweenIssueAndSettlement)) {
            //    return date error
            return $daysBetweenIssueAndSettlement;
        }
        $daysBetweenIssueAndSettlement *= $daysPerYear;
        $daysBetweenIssueAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($issue, $maturity, $basis);
        if (!is_numeric($daysBetweenIssueAndMaturity)) {
            //    return date error
            return $daysBetweenIssueAndMaturity;
        }
        $daysBetweenIssueAndMaturity *= $daysPerYear;
        $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
        if (!is_numeric($daysBetweenSettlementAndMaturity)) {
            //    return date error
            return $daysBetweenSettlementAndMaturity;
        }
        $daysBetweenSettlementAndMaturity *= $daysPerYear;

        return (100 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate * 100)) /
            (1 + (($daysBetweenSettlementAndMaturity / $daysPerYear) * $yield)) -
            (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate * 100);
    }
}
