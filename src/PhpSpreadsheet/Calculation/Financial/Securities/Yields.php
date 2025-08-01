<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Yields
{
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
     * @param mixed $price The security's price per $100 face value
     * @param mixed $redemption The security's redemption value per $100 face value
     * @param mixed $basis The type of day count to use.
     *                       0 or omitted    US (NASD) 30/360
     *                       1               Actual/actual
     *                       2               Actual/360
     *                       3               Actual/365
     *                       4               European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function yieldDiscounted(
        mixed $settlement,
        mixed $maturity,
        mixed $price,
        mixed $redemption,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis) ?? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD;

        try {
            $settlement = SecurityValidations::validateSettlementDate($settlement);
            $maturity = SecurityValidations::validateMaturityDate($maturity);
            SecurityValidations::validateSecurityPeriod($settlement, $maturity);
            $price = SecurityValidations::validatePrice($price);
            $redemption = SecurityValidations::validateRedemption($redemption);
            $basis = SecurityValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(Functions::scalar(DateTimeExcel\DateParts::year($settlement)), $basis);
        if (!is_numeric($daysPerYear)) {
            return $daysPerYear;
        }
        $daysBetweenSettlementAndMaturity = Functions::scalar(DateTimeExcel\YearFrac::fraction($settlement, $maturity, $basis));
        if (!is_numeric($daysBetweenSettlementAndMaturity)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenSettlementAndMaturity);
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
     * @param mixed $rate The security's interest rate at date of issue
     * @param mixed $price The security's price per $100 face value
     * @param mixed $basis The type of day count to use.
     *                       0 or omitted    US (NASD) 30/360
     *                       1               Actual/actual
     *                       2               Actual/360
     *                       3               Actual/365
     *                       4               European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function yieldAtMaturity(
        mixed $settlement,
        mixed $maturity,
        mixed $issue,
        mixed $rate,
        mixed $price,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $issue = Functions::flattenSingleValue($issue);
        $rate = Functions::flattenSingleValue($rate);
        $price = Functions::flattenSingleValue($price);
        $basis = Functions::flattenSingleValue($basis) ?? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD;

        try {
            $settlement = SecurityValidations::validateSettlementDate($settlement);
            $maturity = SecurityValidations::validateMaturityDate($maturity);
            SecurityValidations::validateSecurityPeriod($settlement, $maturity);
            $issue = SecurityValidations::validateIssueDate($issue);
            $rate = SecurityValidations::validateRate($rate);
            $price = SecurityValidations::validatePrice($price);
            $basis = SecurityValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(Functions::scalar(DateTimeExcel\DateParts::year($settlement)), $basis);
        if (!is_numeric($daysPerYear)) {
            return $daysPerYear;
        }
        $daysBetweenIssueAndSettlement = Functions::scalar(DateTimeExcel\YearFrac::fraction($issue, $settlement, $basis));
        if (!is_numeric($daysBetweenIssueAndSettlement)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenIssueAndSettlement);
        }
        $daysBetweenIssueAndSettlement *= $daysPerYear;
        $daysBetweenIssueAndMaturity = Functions::scalar(DateTimeExcel\YearFrac::fraction($issue, $maturity, $basis));
        if (!is_numeric($daysBetweenIssueAndMaturity)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenIssueAndMaturity);
        }
        $daysBetweenIssueAndMaturity *= $daysPerYear;
        $daysBetweenSettlementAndMaturity = Functions::scalar(DateTimeExcel\YearFrac::fraction($settlement, $maturity, $basis));
        if (!is_numeric($daysBetweenSettlementAndMaturity)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenSettlementAndMaturity);
        }
        $daysBetweenSettlementAndMaturity *= $daysPerYear;

        return ((1 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate)
                    - (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate)))
                / (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate)))
            * ($daysPerYear / $daysBetweenSettlementAndMaturity);
    }
}
