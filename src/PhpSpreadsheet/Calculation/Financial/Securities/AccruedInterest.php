<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class AccruedInterest
{
    public const ACCRINT_CALCMODE_ISSUE_TO_SETTLEMENT = true;

    public const ACCRINT_CALCMODE_FIRST_INTEREST_TO_SETTLEMENT = false;

    /**
     * ACCRINT.
     *
     * Returns the accrued interest for a security that pays periodic interest.
     *
     * Excel Function:
     *        ACCRINT(issue,firstinterest,settlement,rate,par,frequency[,basis][,calc_method])
     *
     * @param mixed $issue the security's issue date
     * @param mixed $firstInterest the security's first interest date
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date
     *                                  when the security is traded to the buyer.
     * @param mixed $rate The security's annual coupon rate
     * @param mixed $parValue The security's par value.
     *                            If you omit par, ACCRINT uses $1,000.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use.
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     * @param mixed $calcMethod Unused by PhpSpreadsheet, and apparently by Excel (https://exceljet.net/functions/accrint-function)
     *
     * @return float|string Result, or a string containing an error
     */
    public static function periodic(
        mixed $issue,
        mixed $firstInterest,
        mixed $settlement,
        mixed $rate,
        mixed $parValue = 1000,
        mixed $frequency = FinancialConstants::FREQUENCY_ANNUAL,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD,
        mixed $calcMethod = self::ACCRINT_CALCMODE_ISSUE_TO_SETTLEMENT
    ) {
        $issue = Functions::flattenSingleValue($issue);
        $firstInterest = Functions::flattenSingleValue($firstInterest);
        $settlement = Functions::flattenSingleValue($settlement);
        $rate = Functions::flattenSingleValue($rate);
        $parValue = ($parValue === null) ? 1000 : Functions::flattenSingleValue($parValue);
        $frequency = Functions::flattenSingleValue($frequency) ?? FinancialConstants::FREQUENCY_ANNUAL;
        $basis = Functions::flattenSingleValue($basis) ?? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD;

        try {
            $issue = SecurityValidations::validateIssueDate($issue);
            $settlement = SecurityValidations::validateSettlementDate($settlement);
            SecurityValidations::validateSecurityPeriod($issue, $settlement);
            $rate = SecurityValidations::validateRate($rate);
            $parValue = SecurityValidations::validateParValue($parValue);
            SecurityValidations::validateFrequency($frequency);
            $basis = SecurityValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysBetweenIssueAndSettlement = Functions::scalar(YearFrac::fraction($issue, $settlement, $basis));
        if (!is_numeric($daysBetweenIssueAndSettlement)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenIssueAndSettlement);
        }
        $daysBetweenFirstInterestAndSettlement = Functions::scalar(YearFrac::fraction($firstInterest, $settlement, $basis));
        if (!is_numeric($daysBetweenFirstInterestAndSettlement)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenFirstInterestAndSettlement);
        }

        return $parValue * $rate * $daysBetweenIssueAndSettlement;
    }

    /**
     * ACCRINTM.
     *
     * Returns the accrued interest for a security that pays interest at maturity.
     *
     * Excel Function:
     *        ACCRINTM(issue,settlement,rate[,par[,basis]])
     *
     * @param mixed $issue The security's issue date
     * @param mixed $settlement The security's settlement (or maturity) date
     * @param mixed $rate The security's annual coupon rate
     * @param mixed $parValue The security's par value.
     *                            If you omit parValue, ACCRINT uses $1,000.
     * @param mixed $basis The type of day count to use.
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function atMaturity(
        mixed $issue,
        mixed $settlement,
        mixed $rate,
        mixed $parValue = 1000,
        mixed $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $issue = Functions::flattenSingleValue($issue);
        $settlement = Functions::flattenSingleValue($settlement);
        $rate = Functions::flattenSingleValue($rate);
        $parValue = ($parValue === null) ? 1000 : Functions::flattenSingleValue($parValue);
        $basis = Functions::flattenSingleValue($basis) ?? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD;

        try {
            $issue = SecurityValidations::validateIssueDate($issue);
            $settlement = SecurityValidations::validateSettlementDate($settlement);
            SecurityValidations::validateSecurityPeriod($issue, $settlement);
            $rate = SecurityValidations::validateRate($rate);
            $parValue = SecurityValidations::validateParValue($parValue);
            $basis = SecurityValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysBetweenIssueAndSettlement = Functions::scalar(YearFrac::fraction($issue, $settlement, $basis));
        if (!is_numeric($daysBetweenIssueAndSettlement)) {
            //    return date error
            return StringHelper::convertToString($daysBetweenIssueAndSettlement);
        }

        return $parValue * $rate * $daysBetweenIssueAndSettlement;
    }
}
