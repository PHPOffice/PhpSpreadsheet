<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Coupons
{
    public const FREQUENCY_ANNUAL = 1;
    public const FREQUENCY_SEMI_ANNUAL = 2;
    public const FREQUENCY_QUARTERLY = 4;

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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param int $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYBS($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(DateTime::YEAR($settlement), $basis);
        $prev = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_PREVIOUS);

        if ($basis === Helpers::DAYS_PER_YEAR_ACTUAL) {
            return abs(DateTime::DAYS($prev, $settlement));
        }

        return DateTime::YEARFRAC($prev, $settlement, $basis) * $daysPerYear;
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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYS($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        switch ($basis) {
            case Helpers::DAYS_PER_YEAR_365:
                // Actual/365
                return 365 / $frequency;
            case Helpers::DAYS_PER_YEAR_ACTUAL:
                // Actual/actual
                if ($frequency == self::FREQUENCY_ANNUAL) {
                    $daysPerYear = Helpers::daysPerYear(DateTime::YEAR($settlement), $basis);

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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string
     */
    public static function COUPDAYSNC($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $daysPerYear = Helpers::daysPerYear(DateTime::YEAR($settlement), $basis);
        $next = self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_NEXT);

        if ($basis === Helpers::DAYS_PER_YEAR_NASD) {
            $settlementDate = Date::excelToDateTimeObject($settlement);
            $settlementEoM = self::isLastDayOfMonth($settlementDate);
            if ($settlementEoM) {
                ++$settlement;
            }
        }

        return DateTime::YEARFRAC($settlement, $next, $basis) * $daysPerYear;
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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function COUPNCD($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return int|string
     */
    public static function COUPNUM($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $yearsBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, 0);

        return ceil($yearsBetweenSettlementAndMaturity * $frequency);
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
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed $frequency the number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function COUPPCD($settlement, $maturity, $frequency, $basis = Helpers::DAYS_PER_YEAR_NASD)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        try {
            $settlement = self::validateSettlementDate($settlement);
            $maturity = self::validateMaturityDate($maturity);
            self::validateCouponPeriod($settlement, $maturity);
            $frequency = self::validateFrequency($frequency);
            $basis = self::validateBasis($basis);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::couponFirstPeriodDate($settlement, $maturity, $frequency, self::PERIOD_DATE_PREVIOUS);
    }

    /**
     * isLastDayOfMonth.
     *
     * Returns a boolean TRUE/FALSE indicating if this date is the last date of the month
     *
     * @param \DateTime $testDate The date for testing
     *
     * @return bool
     */
    private static function isLastDayOfMonth(\DateTime $testDate)
    {
        return $testDate->format('d') === $testDate->format('t');
    }

    private static function couponFirstPeriodDate($settlement, $maturity, int $frequency, $next)
    {
        $months = 12 / $frequency;

        $result = Date::excelToDateTimeObject($maturity);
        $maturityEoM = self::isLastDayOfMonth($result);

        while ($settlement < Date::PHPToExcel($result)) {
            $result->modify('-' . $months . ' months');
        }
        if ($next === true) {
            $result->modify('+' . $months . ' months');
        }

        if ($maturityEoM === true) {
            $result->modify('-1 day');
        }

        return Date::PHPToExcel($result);
    }

    private static function validateInputDate($date)
    {
        $date = DateTime::getDateValue($date);
        if (is_string($date)) {
            throw new Exception(Functions::VALUE());
        }

        return $date;
    }

    private static function validateSettlementDate($settlement)
    {
        return self::validateInputDate($settlement);
    }

    private static function validateMaturityDate($maturity)
    {
        return self::validateInputDate($maturity);
    }

    private static function validateCouponPeriod($settlement, $maturity): void
    {
        if ($settlement >= $maturity) {
            throw new Exception(Functions::NAN());
        }
    }

    private static function validateFrequency($frequency): int
    {
        if (!is_numeric($frequency)) {
            throw new Exception(Functions::NAN());
        }

        $frequency = (int) $frequency;
        if (
            ($frequency !== self::FREQUENCY_ANNUAL) &&
            ($frequency !== self::FREQUENCY_SEMI_ANNUAL) &&
            ($frequency !== self::FREQUENCY_QUARTERLY)
        ) {
            throw new Exception(Functions::NAN());
        }

        return $frequency;
    }

    private static function validateBasis($basis): int
    {
        if (!is_numeric($basis)) {
            throw new Exception(Functions::NAN());
        }

        $basis = (int) $basis;
        if (($basis < 0) || ($basis > 4)) {
            throw new Exception(Functions::NAN());
        }

        return $basis;
    }
}
