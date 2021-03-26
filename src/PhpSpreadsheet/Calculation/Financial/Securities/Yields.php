<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Yields
{
    use BaseValidations;

    /**
     * YIELDDISC.
     *
     * Returns the annual yield of a security that pays interest at maturity.
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security's settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param int $price The security's price per $100 face value
     * @param int $redemption The security's redemption value per $100 face value
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function yieldDiscounted($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = (int) Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateSecurityPeriod($settlement, $maturity);
            $price = self::validatePrice($price);
            $redemption = self::validateRedemption($redemption);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(DateTimeExcel\Year::funcYear($settlement), $basis);
        if (!is_numeric($daysPerYear)) {
            return $daysPerYear;
        }
        $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
        if (!is_numeric($daysBetweenSettlementAndMaturity)) {
            //    return date error
            return $daysBetweenSettlementAndMaturity;
        }
        $daysBetweenSettlementAndMaturity *= $daysPerYear;

        return (($redemption - $price) / $price) * ($daysPerYear / $daysBetweenSettlementAndMaturity);
    }

    /**
     * YIELDMAT.
     *
     * Returns the annual yield of a security that pays interest at maturity.
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security's settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $issue The security's issue date
     * @param int $rate The security's interest rate at date of issue
     * @param int $price The security's price per $100 face value
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function yieldAtMaturity($settlement, $maturity, $issue, $rate, $price, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $issue = Functions::flattenSingleValue($issue);
        $rate = Functions::flattenSingleValue($rate);
        $price = Functions::flattenSingleValue($price);
        $basis = Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateSecurityPeriod($settlement, $maturity);
            $issue = self::validateIssueDate($issue);
            $rate = self::validateRate($rate);
            $price = self::validatePrice($price);
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

        return ((1 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate) - (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) /
                (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) *
            ($daysPerYear / $daysBetweenSettlementAndMaturity);
    }
}
