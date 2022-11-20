<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Coupons
{
    private const PERIOD_DATE_PREVIOUS = false;
    private const PERIOD_DATE_NEXT = true;

    /**
     * COUPDAYBS.
     *
     * Returns the number of days from the beginning of the coupon period to the settlement date.
     *
     * Excel Function:
     *        COUPDAYBS(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                                  date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year (int).
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYBS(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(Functions::scalar(DateTimeExcel\DateParts::year($settlement)), $basis);
        if (is_string($daysPerYear)) {
            return ExcelError::VALUE();
        }
        $prev = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_PREVIOUS);

        if ($basis === FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL) {
            return abs((float) DateTimeExcel\Days::between($prev, $settlement));
        }

        return (float) DateTimeExcel\YearFrac::fraction($prev, $settlement, $basis) * $daysPerYear;
    }

    /**
     * COUPDAYS.
     *
     * Returns the number of days in the coupon period that contains the settlement date.
     *
     * Excel Function:
     *        COUPDAYS(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                                  date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYS(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        switch ($basis) {
            case FinancialConstants::BASIS_DAYS_PER_YEAR_365:
                // Actual/365
                return 365 / $frequency;
            case FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL:
                // Actual/actual
                if ($frequency == FinancialConstants::FREQUENCY_ANNUAL) {
                    $daysPerYear = (int) Helpers::daysPerYear(Functions::scalar(DateTimeExcel\DateParts::year($settlement)), $basis);

                    return $daysPerYear / $frequency;
                }
                $prev = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_PREVIOUS);
                $next = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_NEXT);

                return $next - $prev;
            default:
                // US (NASD) 30/360, Actual/360 or European 30/360
                return 360 / $frequency;
        }
    }

    /**
     * COUPDAYSNC.
     *
     * Returns the number of days from the settlement date to the next coupon date.
     *
     * Excel Function:
     *        COUPDAYSNC(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                                  date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int) .
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYSNC(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        /** @var int */
        $daysPerYear = Helpers::daysPerYear(DateTimeExcel\DateParts::year($settlement), $basis);
        $next = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_NEXT);

        if ($basis === FinancialConstants::BASIS_DAYS_PER_YEAR_NASD) {
            $settlementDate = Date::excelToDateTimeObject($settlement);
            $settlementEoM = Helpers::isLastDayOfMonth($settlementDate);
            if ($settlementEoM) {
                ++$settlement;
            }
        }

        return (float) DateTimeExcel\YearFrac::fraction($settlement, $next, $basis) * $daysPerYear;
    }

    /**
     * COUPNCD.
     *
     * Returns the next coupon date after the settlement date.
     *
     * Excel Function:
     *        COUPNCD(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                                  date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                     depending on the value of the ReturnDateType flag
     */
    public static function COUPNCD(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
            self::doNothing($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_NEXT);
    }

    /**
     * COUPNUM.
     *
     * Returns the number of coupons payable between the settlement date and maturity date,
     * rounded up to the nearest whole coupon.
     *
     * Excel Function:
     *        COUPNUM(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                                  date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return int|string
     */
    public static function COUPNUM(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
            self::doNothing($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $yearsBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::fraction(
            $settlement,
            $maturity,
            FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
        );

        return (int) ceil((float) $yearsBetweenSettlementAndMaturity * $frequency);
    }

    /**
     * COUPPCD.
     *
     * Returns the previous coupon date before the settlement date.
     *
     * Excel Function:
     *        COUPPCD(settlement,maturity,frequency[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue
     *                              date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $frequency The number of coupon payments per year.
     *                             Valid frequency values are:
     *                               1    Annual
     *                               2    Semi-Annual
     *                               4    Quarterly
     * @param mixed $basis The type of day count to use (int).
     *                         0 or omitted    US (NASD) 30/360
     *                         1               Actual/actual
     *                         2               Actual/360
     *                         3               Actual/365
     *                         4               European 30/360
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                     depending on the value of the ReturnDateType flag
     */
    public static function COUPPCD(
        $settlement,
        $maturity,
        $frequency,
        $basis = FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
    ) {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null)
            ? FinancialConstants::BASIS_DAYS_PER_YEAR_NASD
            : Functions::flattenSingleValue($basis);

        try {
            $settlement = FinancialValidations::validateSettlementDate($settlement);
            $maturity = FinancialValidations::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = FinancialValidations::validateFrequency($frequency);
            $basis = FinancialValidations::validateBasis($basis);
            self::doNothing($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_PREVIOUS);
    }

    private static function monthsDiff(DateTime $result, int $months, string $plusOrMinus, int $day, bool $lastDayFlag): void
    {
        $result->setDate((int) $result->format('Y'), (int) $result->format('m'), 1);
        $result->modify("$plusOrMinus $months months");
        $daysInMonth = (int) $result->format('t');
        $result->setDate((int) $result->format('Y'), (int) $result->format('m'), $lastDayFlag ? $daysInMonth : min($day, $daysInMonth));
    }

    private static function couponFirstPeriodDate(float $settlement, float $maturity, int $frequency, bool $next): float
    {
        $months = 12 / $frequency;

        $result = Date::excelToDateTimeObject($maturity);
        $day = (int) $result->format('d');
        $lastDayFlag = Helpers::isLastDayOfMonth($result);

        while ($settlement < Date::PHPToExcel($result)) {
            self::monthsDiff($result, $months, '-', $day, $lastDayFlag);
        }
        if ($next === true) {
            self::monthsDiff($result, $months, '+', $day, $lastDayFlag);
        }

        return (float) Date::PHPToExcel($result);
    }

    private static function validateCouponPeriod(float $settlement, float $maturity): void
    {
        if ($settlement >= $maturity) {
            throw new Exception(ExcelError::NAN());
        }
    }

    /** @param mixed $basis */
    private static function doNothing($basis): bool
    {
        return $basis;
    }
}
