<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill;

class Financial
{
    const FINANCIAL_MAX_ITERATIONS = 128;

    const FINANCIAL_PRECISION = 1.0e-08;

    private static function interestAndPrincipal($rate = 0, $per = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $pmt = self::PMT($rate, $nper, $pv, $fv, $type);
        $capital = $pv;
        for ($i = 1; $i <= $per; ++$i) {
            $interest = ($type && $i == 1) ? 0 : -$capital * $rate;
            $principal = $pmt - $interest;
            $capital += $principal;
        }

        return [$interest, $principal];
    }

    /**
     * ACCRINT.
     *
     * Returns the accrued interest for a security that pays periodic interest.
     *
     * Excel Function:
     *        ACCRINT(issue,firstinterest,settlement,rate,par,frequency[,basis][,calc_method])
     *
     * @Deprecated 1.18.0
     *
     * @see Financial\Securities\AccruedInterest::periodic()
     *      Use the periodic() method in the Financial\Securities\AccruedInterest class instead
     *
     * @param mixed $issue the security's issue date
     * @param mixed $firstinterest the security's first interest date
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date
     *                                  when the security is traded to the buyer.
     * @param mixed (float) $rate the security's annual coupon rate
     * @param mixed (float) $par The security's par value.
     *                               If you omit par, ACCRINT uses $1,000.
     * @param mixed (int) $frequency The number of coupon payments per year.
     *                                    Valid frequency values are:
     *                                        1    Annual
     *                                        2    Semi-Annual
     *                                        4    Quarterly
     * @param mixed (int) $basis The type of day count to use.
     *                               0 or omitted    US (NASD) 30/360
     *                               1                Actual/actual
     *                               2                Actual/360
     *                               3                Actual/365
     *                               4                European 30/360
     * @param mixed (bool) $calcMethod
     *                          If true, use Issue to Settlement
     *                          If false, use FirstInterest to Settlement
     *
     * @return float|string Result, or a string containing an error
     */
    public static function ACCRINT(
        $issue,
        $firstinterest,
        $settlement,
        $rate,
        $par = 1000,
        $frequency = 1,
        $basis = 0,
        $calcMethod = true
    ) {
        return Securities\AccruedInterest::periodic(
            $issue,
            $firstinterest,
            $settlement,
            $rate,
            $par,
            $frequency,
            $basis,
            $calcMethod
        );
    }

    /**
     * ACCRINTM.
     *
     * Returns the accrued interest for a security that pays interest at maturity.
     *
     * Excel Function:
     *        ACCRINTM(issue,settlement,rate[,par[,basis]])
     *
     * @Deprecated 1.18.0
     *
     * @see Financial\Securities\AccruedInterest::atMaturity()
     *      Use the atMaturity() method in the Financial\Securities\AccruedInterest class instead
     *
     * @param mixed $issue The security's issue date
     * @param mixed $settlement The security's settlement (or maturity) date
     * @param mixed (float) $rate The security's annual coupon rate
     * @param mixed (float) $par The security's par value.
     *                               If you omit par, ACCRINT uses $1,000.
     * @param mixed (int) $basis The type of day count to use.
     *                               0 or omitted    US (NASD) 30/360
     *                               1                Actual/actual
     *                               2                Actual/360
     *                               3                Actual/365
     *                               4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function ACCRINTM($issue, $settlement, $rate, $par = 1000, $basis = 0)
    {
        return Securities\AccruedInterest::atMaturity($issue, $settlement, $rate, $par, $basis);
    }

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
     * @Deprecated 1.18.0
     *
     * @see Use the AMORDEGRC() method in the Financial\Amortization class instead
     *
     * @param float $cost The cost of the asset
     * @param mixed $purchased Date of the purchase of the asset
     * @param mixed $firstPeriod Date of the end of the first period
     * @param mixed $salvage The salvage value at the end of the life of the asset
     * @param float $period The period
     * @param float $rate Rate of depreciation
     * @param int $basis The type of day count to use.
     *                       0 or omitted    US (NASD) 30/360
     *                       1                Actual/actual
     *                       2                Actual/360
     *                       3                Actual/365
     *                       4                European 30/360
     *
     * @return float|string (string containing the error type if there is an error)
     */
    public static function AMORDEGRC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        return Amortization::AMORDEGRC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis);
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
     * @Deprecated 1.18.0
     *
     * @see Use the AMORLINC() method in the Financial\Amortization class instead
     *
     * @param float $cost The cost of the asset
     * @param mixed $purchased Date of the purchase of the asset
     * @param mixed $firstPeriod Date of the end of the first period
     * @param mixed $salvage The salvage value at the end of the life of the asset
     * @param float $period The period
     * @param float $rate Rate of depreciation
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string (string containing the error type if there is an error)
     */
    public static function AMORLINC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        return Amortization::AMORLINC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis);
    }

    /**
     * COUPDAYBS.
     *
     * Returns the number of days from the beginning of the coupon period to the settlement date.
     *
     * Excel Function:
     *        COUPDAYBS(settlement,maturity,frequency[,basis])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COUPDAYBS() method in the Financial\Coupons class instead
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
    public static function COUPDAYBS($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPDAYBS($settlement, $maturity, $frequency, $basis);
    }

    /**
     * COUPDAYS.
     *
     * Returns the number of days in the coupon period that contains the settlement date.
     *
     * Excel Function:
     *        COUPDAYS(settlement,maturity,frequency[,basis])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COUPDAYS() method in the Financial\Coupons class instead
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
    public static function COUPDAYS($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPDAYS($settlement, $maturity, $frequency, $basis);
    }

    /**
     * COUPDAYSNC.
     *
     * Returns the number of days from the settlement date to the next coupon date.
     *
     * Excel Function:
     *        COUPDAYSNC(settlement,maturity,frequency[,basis])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COUPDAYSNC() method in the Financial\Coupons class instead
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
    public static function COUPDAYSNC($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPDAYSNC($settlement, $maturity, $frequency, $basis);
    }

    /**
     * COUPNCD.
     *
     * Returns the next coupon date after the settlement date.
     *
     * Excel Function:
     *        COUPNCD(settlement,maturity,frequency[,basis])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COUPNCD() method in the Financial\Coupons class instead
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
    public static function COUPNCD($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPNCD($settlement, $maturity, $frequency, $basis);
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
     * @Deprecated 1.18.0
     *
     * @see Use the COUPNUM() method in the Financial\Coupons class instead
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
    public static function COUPNUM($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPNUM($settlement, $maturity, $frequency, $basis);
    }

    /**
     * COUPPCD.
     *
     * Returns the previous coupon date before the settlement date.
     *
     * Excel Function:
     *        COUPPCD(settlement,maturity,frequency[,basis])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COUPPCD() method in the Financial\Coupons class instead
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
    public static function COUPPCD($settlement, $maturity, $frequency, $basis = 0)
    {
        return Coupons::COUPPCD($settlement, $maturity, $frequency, $basis);
    }

    /**
     * CUMIPMT.
     *
     * Returns the cumulative interest paid on a loan between the start and end periods.
     *
     * Excel Function:
     *        CUMIPMT(rate,nper,pv,start,end[,type])
     *
     * @param float $rate The Interest rate
     * @param int $nper The total number of payment periods
     * @param float $pv Present Value
     * @param int $start The first period in the calculation.
     *                            Payment periods are numbered beginning with 1.
     * @param int $end the last period in the calculation
     * @param int $type A number 0 or 1 and indicates when payments are due:
     *                                0 or omitted    At the end of the period.
     *                                1                At the beginning of the period.
     *
     * @return float|string
     */
    public static function CUMIPMT($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $start = (int) Functions::flattenSingleValue($start);
        $end = (int) Functions::flattenSingleValue($end);
        $type = (int) Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        // Calculate
        $interest = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ipmt = self::IPMT($rate, $per, $nper, $pv, 0, $type);
            if (is_string($ipmt)) {
                return $ipmt;
            }

            $interest += $ipmt;
        }

        return $interest;
    }

    /**
     * CUMPRINC.
     *
     * Returns the cumulative principal paid on a loan between the start and end periods.
     *
     * Excel Function:
     *        CUMPRINC(rate,nper,pv,start,end[,type])
     *
     * @param float $rate The Interest rate
     * @param int $nper The total number of payment periods
     * @param float $pv Present Value
     * @param int $start The first period in the calculation.
     *                            Payment periods are numbered beginning with 1.
     * @param int $end the last period in the calculation
     * @param int $type A number 0 or 1 and indicates when payments are due:
     *                                0 or omitted    At the end of the period.
     *                                1                At the beginning of the period.
     *
     * @return float|string
     */
    public static function CUMPRINC($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $start = (int) Functions::flattenSingleValue($start);
        $end = (int) Functions::flattenSingleValue($end);
        $type = (int) Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        // Calculate
        $principal = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ppmt = self::PPMT($rate, $per, $nper, $pv, 0, $type);
            if (is_string($ppmt)) {
                return $ppmt;
            }

            $principal += $ppmt;
        }

        return $principal;
    }

    /**
     * DB.
     *
     * Returns the depreciation of an asset for a specified period using the
     * fixed-declining balance method.
     * This form of depreciation is used if you want to get a higher depreciation value
     * at the beginning of the depreciation (as opposed to linear depreciation). The
     * depreciation value is reduced with every depreciation period by the depreciation
     * already deducted from the initial cost.
     *
     * Excel Function:
     *        DB(cost,salvage,life,period[,month])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the DB() method in the Financial\Depreciation class instead
     *
     * @param float $cost Initial cost of the asset
     * @param float $salvage Value at the end of the depreciation.
     *                                (Sometimes called the salvage value of the asset)
     * @param int $life Number of periods over which the asset is depreciated.
     *                                (Sometimes called the useful life of the asset)
     * @param int $period The period for which you want to calculate the
     *                                depreciation. Period must use the same units as life.
     * @param int $month Number of months in the first year. If month is omitted,
     *                                it defaults to 12.
     *
     * @return float|string
     */
    public static function DB($cost, $salvage, $life, $period, $month = 12)
    {
        return Depreciation::DB($cost, $salvage, $life, $period, $month);
    }

    /**
     * DDB.
     *
     * Returns the depreciation of an asset for a specified period using the
     * double-declining balance method or some other method you specify.
     *
     * Excel Function:
     *        DDB(cost,salvage,life,period[,factor])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the DDB() method in the Financial\Depreciation class instead
     *
     * @param float $cost Initial cost of the asset
     * @param float $salvage Value at the end of the depreciation.
     *                                (Sometimes called the salvage value of the asset)
     * @param int $life Number of periods over which the asset is depreciated.
     *                                (Sometimes called the useful life of the asset)
     * @param int $period The period for which you want to calculate the
     *                                depreciation. Period must use the same units as life.
     * @param float $factor The rate at which the balance declines.
     *                                If factor is omitted, it is assumed to be 2 (the
     *                                double-declining balance method).
     *
     * @return float|string
     */
    public static function DDB($cost, $salvage, $life, $period, $factor = 2.0)
    {
        return Depreciation::DDB($cost, $salvage, $life, $period, $factor);
    }

    /**
     * DISC.
     *
     * Returns the discount rate for a security.
     *
     * Excel Function:
     *        DISC(settlement,maturity,price,redemption[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                                The security settlement date is the date after the issue
     *                                date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param int $price The security's price per $100 face value
     * @param int $redemption The security's redemption value per $100 face value
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string
     */
    public static function DISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis);

        //    Validate
        if ((is_numeric($price)) && (is_numeric($redemption)) && (is_numeric($basis))) {
            $price = (float) $price;
            $redemption = (float) $redemption;
            $basis = (int) $basis;
            if (($price <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                //    return date error
                return $daysBetweenSettlementAndMaturity;
            }

            return (1 - $price / $redemption) / $daysBetweenSettlementAndMaturity;
        }

        return Functions::VALUE();
    }

    /**
     * DOLLARDE.
     *
     * Converts a dollar price expressed as an integer part and a fraction
     *        part into a dollar price expressed as a decimal number.
     * Fractional dollar numbers are sometimes used for security prices.
     *
     * Excel Function:
     *        DOLLARDE(fractional_dollar,fraction)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the decimal() method in the Financial\Dollar class instead
     *
     * @param float $fractional_dollar Fractional Dollar
     * @param int $fraction Fraction
     *
     * @return float|string
     */
    public static function DOLLARDE($fractional_dollar = null, $fraction = 0)
    {
        return Dollar::decimal($fractional_dollar, $fraction);
    }

    /**
     * DOLLARFR.
     *
     * Converts a dollar price expressed as a decimal number into a dollar price
     *        expressed as a fraction.
     * Fractional dollar numbers are sometimes used for security prices.
     *
     * Excel Function:
     *        DOLLARFR(decimal_dollar,fraction)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the fractional() method in the Financial\Dollar class instead
     *
     * @param float $decimal_dollar Decimal Dollar
     * @param int $fraction Fraction
     *
     * @return float|string
     */
    public static function DOLLARFR($decimal_dollar = null, $fraction = 0)
    {
        return Dollar::fractional($decimal_dollar, $fraction);
    }

    /**
     * EFFECT.
     *
     * Returns the effective interest rate given the nominal rate and the number of
     *        compounding payments per year.
     *
     * Excel Function:
     *        EFFECT(nominal_rate,npery)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the effective() method in the Financial\InterestRate class instead
     *
     * @param float $nominalRate Nominal interest rate
     * @param int $periodsPerYear Number of compounding payments per year
     *
     * @return float|string
     */
    public static function EFFECT($nominalRate = 0, $periodsPerYear = 0)
    {
        return Financial\InterestRate::effective($nominalRate, $periodsPerYear);
    }

    /**
     * FV.
     *
     * Returns the Future Value of a cash flow with constant payments and interest rate (annuities).
     *
     * Excel Function:
     *        FV(rate,nper,pmt[,pv[,type]])
     *
     * @param float $rate The interest rate per period
     * @param int $nper Total number of payment periods in an annuity
     * @param float $pmt The payment made each period: it cannot change over the
     *                            life of the annuity. Typically, pmt contains principal
     *                            and interest but no other fees or taxes.
     * @param float $pv present Value, or the lump-sum amount that a series of
     *                            future payments is worth right now
     * @param int $type A number 0 or 1 and indicates when payments are due:
     *                                0 or omitted    At the end of the period.
     *                                1                At the beginning of the period.
     *
     * @return float|string
     */
    public static function FV($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $type = Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            return -$pv * (1 + $rate) ** $nper - $pmt * (1 + $rate * $type) * ((1 + $rate) ** $nper - 1) / $rate;
        }

        return -$pv - $pmt * $nper;
    }

    /**
     * FVSCHEDULE.
     *
     * Returns the future value of an initial principal after applying a series of compound interest rates.
     * Use FVSCHEDULE to calculate the future value of an investment with a variable or adjustable rate.
     *
     * Excel Function:
     *        FVSCHEDULE(principal,schedule)
     *
     * @param float $principal the present value
     * @param float[] $schedule an array of interest rates to apply
     *
     * @return float
     */
    public static function FVSCHEDULE($principal, $schedule)
    {
        $principal = Functions::flattenSingleValue($principal);
        $schedule = Functions::flattenArray($schedule);

        foreach ($schedule as $rate) {
            $principal *= 1 + $rate;
        }

        return $principal;
    }

    /**
     * INTRATE.
     *
     * Returns the interest rate for a fully invested security.
     *
     * Excel Function:
     *        INTRATE(settlement,maturity,investment,redemption[,basis])
     *
     * @param mixed $settlement The security's settlement date.
     *                                The security settlement date is the date after the issue date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param int $investment the amount invested in the security
     * @param int $redemption the amount to be received at maturity
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string
     */
    public static function INTRATE($settlement, $maturity, $investment, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $investment = Functions::flattenSingleValue($investment);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis);

        //    Validate
        if ((is_numeric($investment)) && (is_numeric($redemption)) && (is_numeric($basis))) {
            $investment = (float) $investment;
            $redemption = (float) $redemption;
            $basis = (int) $basis;
            if (($investment <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                //    return date error
                return $daysBetweenSettlementAndMaturity;
            }

            return (($redemption / $investment) - 1) / ($daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }

    /**
     * IPMT.
     *
     * Returns the interest payment for a given period for an investment based on periodic, constant payments and a constant interest rate.
     *
     * Excel Function:
     *        IPMT(rate,per,nper,pv[,fv][,type])
     *
     * @param float $rate Interest rate per period
     * @param int $per Period for which we want to find the interest
     * @param int $nper Number of periods
     * @param float $pv Present Value
     * @param float $fv Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string
     */
    public static function IPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $per = (int) Functions::flattenSingleValue($per);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = (int) Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($per <= 0 || $per > $nper) {
            return Functions::NAN();
        }

        // Calculate
        $interestAndPrincipal = self::interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);

        return $interestAndPrincipal[0];
    }

    /**
     * IRR.
     *
     * Returns the internal rate of return for a series of cash flows represented by the numbers in values.
     * These cash flows do not have to be even, as they would be for an annuity. However, the cash flows must occur
     * at regular intervals, such as monthly or annually. The internal rate of return is the interest rate received
     * for an investment consisting of payments (negative values) and income (positive values) that occur at regular
     * periods.
     *
     * Excel Function:
     *        IRR(values[,guess])
     *
     * @param mixed (float[]) $values An array or a reference to cells that contain numbers for which you want
     *                                    to calculate the internal rate of return.
     *                                Values must contain at least one positive value and one negative value to
     *                                    calculate the internal rate of return.
     * @param mixed (float) $guess A number that you guess is close to the result of IRR
     *
     * @return float|string
     */
    public static function IRR($values, $guess = 0.1)
    {
        if (!is_array($values)) {
            return Functions::VALUE();
        }
        $values = Functions::flattenArray($values);
        $guess = Functions::flattenSingleValue($guess);

        // create an initial range, with a root somewhere between 0 and guess
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = self::NPV($x1, $values);
        $f2 = self::NPV($x2, $values);
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::NPV($x1 += 1.6 * ($x1 - $x2), $values);
            } else {
                $f2 = self::NPV($x2 += 1.6 * ($x2 - $x1), $values);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return Functions::VALUE();
        }

        $f = self::NPV($x1, $values);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::NPV($x_mid, $values);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                return $x_mid;
            }
        }

        return Functions::VALUE();
    }

    /**
     * ISPMT.
     *
     * Returns the interest payment for an investment based on an interest rate and a constant payment schedule.
     *
     * Excel Function:
     *     =ISPMT(interest_rate, period, number_payments, PV)
     *
     * interest_rate is the interest rate for the investment
     *
     * period is the period to calculate the interest rate.  It must be betweeen 1 and number_payments.
     *
     * number_payments is the number of payments for the annuity
     *
     * PV is the loan amount or present value of the payments
     */
    public static function ISPMT(...$args)
    {
        // Return value
        $returnValue = 0;

        // Get the parameters
        $aArgs = Functions::flattenArray($args);
        $interestRate = array_shift($aArgs);
        $period = array_shift($aArgs);
        $numberPeriods = array_shift($aArgs);
        $principleRemaining = array_shift($aArgs);

        // Calculate
        $principlePayment = ($principleRemaining * 1.0) / ($numberPeriods * 1.0);
        for ($i = 0; $i <= $period; ++$i) {
            $returnValue = $interestRate * $principleRemaining * -1;
            $principleRemaining -= $principlePayment;
            // principle needs to be 0 after the last payment, don't let floating point screw it up
            if ($i == $numberPeriods) {
                $returnValue = 0;
            }
        }

        return $returnValue;
    }

    /**
     * MIRR.
     *
     * Returns the modified internal rate of return for a series of periodic cash flows. MIRR considers both
     *        the cost of the investment and the interest received on reinvestment of cash.
     *
     * Excel Function:
     *        MIRR(values,finance_rate, reinvestment_rate)
     *
     * @param mixed (float[]) $values An array or a reference to cells that contain a series of payments and
     *                                            income occurring at regular intervals.
     *                                        Payments are negative value, income is positive values.
     * @param mixed (float) $finance_rate The interest rate you pay on the money used in the cash flows
     * @param mixed (float) $reinvestment_rate The interest rate you receive on the cash flows as you reinvest them
     *
     * @return float|string Result, or a string containing an error
     */
    public static function MIRR($values, $finance_rate, $reinvestment_rate)
    {
        if (!is_array($values)) {
            return Functions::VALUE();
        }
        $values = Functions::flattenArray($values);
        $finance_rate = Functions::flattenSingleValue($finance_rate);
        $reinvestment_rate = Functions::flattenSingleValue($reinvestment_rate);
        $n = count($values);

        $rr = 1.0 + $reinvestment_rate;
        $fr = 1.0 + $finance_rate;

        $npv_pos = $npv_neg = 0.0;
        foreach ($values as $i => $v) {
            if ($v >= 0) {
                $npv_pos += $v / $rr ** $i;
            } else {
                $npv_neg += $v / $fr ** $i;
            }
        }

        if (($npv_neg == 0) || ($npv_pos == 0) || ($reinvestment_rate <= -1)) {
            return Functions::VALUE();
        }

        $mirr = ((-$npv_pos * $rr ** $n)
                / ($npv_neg * ($rr))) ** (1.0 / ($n - 1)) - 1.0;

        return is_finite($mirr) ? $mirr : Functions::VALUE();
    }

    /**
     * NOMINAL.
     *
     * Returns the nominal interest rate given the effective rate and the number of compounding payments per year.
     *
     * Excel Function:
     *        NOMINAL(effect_rate, npery)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the nominal() method in the Financial\InterestRate class instead
     *
     * @param float $effectiveRate Effective interest rate
     * @param int $periodsPerYear Number of compounding payments per year
     *
     * @return float|string Result, or a string containing an error
     */
    public static function NOMINAL($effectiveRate = 0, $periodsPerYear = 0)
    {
        return InterestRate::nominal($effectiveRate, $periodsPerYear);
    }

    /**
     * NPER.
     *
     * Returns the number of periods for a cash flow with constant periodic payments (annuities), and interest rate.
     *
     * @param float $rate Interest rate per period
     * @param int $pmt Periodic payment (annuity)
     * @param float $pv Present Value
     * @param float $fv Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function NPER($rate = 0, $pmt = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            if ($pmt == 0 && $pv == 0) {
                return Functions::NAN();
            }

            return log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        }
        if ($pmt == 0) {
            return Functions::NAN();
        }

        return (-$pv - $fv) / $pmt;
    }

    /**
     * NPV.
     *
     * Returns the Net Present Value of a cash flow series given a discount rate.
     *
     * @return float
     */
    public static function NPV(...$args)
    {
        // Return value
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $rate = array_shift($aArgs);
        $countArgs = count($aArgs);
        for ($i = 1; $i <= $countArgs; ++$i) {
            // Is it a numeric value?
            if (is_numeric($aArgs[$i - 1])) {
                $returnValue += $aArgs[$i - 1] / (1 + $rate) ** $i;
            }
        }

        // Return
        return $returnValue;
    }

    /**
     * PDURATION.
     *
     * Calculates the number of periods required for an investment to reach a specified value.
     *
     * @param float $rate Interest rate per period
     * @param float $pv Present Value
     * @param float $fv Future Value
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PDURATION($rate = 0, $pv = 0, $fv = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);

        // Validate parameters
        if (!is_numeric($rate) || !is_numeric($pv) || !is_numeric($fv)) {
            return Functions::VALUE();
        } elseif ($rate <= 0.0 || $pv <= 0.0 || $fv <= 0.0) {
            return Functions::NAN();
        }

        return (log($fv) - log($pv)) / log(1 + $rate);
    }

    /**
     * PMT.
     *
     * Returns the constant payment (annuity) for a cash flow with a constant interest rate.
     *
     * @param float $rate Interest rate per period
     * @param int $nper Number of periods
     * @param float $pv Present Value
     * @param float $fv Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            return (-$fv - $pv * (1 + $rate) ** $nper) / (1 + $rate * $type) / (((1 + $rate) ** $nper - 1) / $rate);
        }

        return (-$pv - $fv) / $nper;
    }

    /**
     * PPMT.
     *
     * Returns the interest payment for a given period for an investment based on periodic, constant payments and a constant interest rate.
     *
     * @param float $rate Interest rate per period
     * @param int $per Period for which we want to find the interest
     * @param int $nper Number of periods
     * @param float $pv Present Value
     * @param float $fv Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $per = (int) Functions::flattenSingleValue($per);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = (int) Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($per <= 0 || $per > $nper) {
            return Functions::NAN();
        }

        // Calculate
        $interestAndPrincipal = self::interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);

        return $interestAndPrincipal[1];
    }

    /**
     * PRICE.
     *
     * Returns the price per $100 face value of a security that pays periodic interest.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the price() method in the Financial\Securities\Price class instead
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param float $rate the security's annual coupon rate
     * @param float $yield the security's annual yield
     * @param float $redemption The number of coupon payments per year.
     *                              For annual payments, frequency = 1;
     *                              for semiannual, frequency = 2;
     *                              for quarterly, frequency = 4.
     * @param int $frequency
     * @param int $basis The type of day count to use.
     *                       0 or omitted    US (NASD) 30/360
     *                       1                Actual/actual
     *                       2                Actual/360
     *                       3                Actual/365
     *                       4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PRICE($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis = 0)
    {
        return Securities\Price::price($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis);
    }

    /**
     * PRICEDISC.
     *
     * Returns the price per $100 face value of a discounted security.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the priceDiscounted() method in the Financial\Securities\Price class instead
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param int $discount The security's discount rate
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
    public static function PRICEDISC($settlement, $maturity, $discount, $redemption, $basis = 0)
    {
        return Securities\Price::priceDiscounted($settlement, $maturity, $discount, $redemption, $basis);
    }

    /**
     * PRICEMAT.
     *
     * Returns the price per $100 face value of a security that pays interest at maturity.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the priceAtMaturity() method in the Financial\Securities\Price class instead
     *
     * @param mixed $settlement The security's settlement date.
     *                              The security's settlement date is the date after the issue date when the security
     *                              is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                            The maturity date is the date when the security expires.
     * @param mixed $issue The security's issue date
     * @param int $rate The security's interest rate at date of issue
     * @param int $yield The security's annual yield
     * @param int $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PRICEMAT($settlement, $maturity, $issue, $rate, $yield, $basis = 0)
    {
        return Securities\Price::priceAtMaturity($settlement, $maturity, $issue, $rate, $yield, $basis);
    }

    /**
     * PV.
     *
     * Returns the Present Value of a cash flow with constant payments and interest rate (annuities).
     *
     * @param float $rate Interest rate per period
     * @param int $nper Number of periods
     * @param float $pmt Periodic payment (annuity)
     * @param float $fv Future Value
     * @param int $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PV($rate = 0, $nper = 0, $pmt = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            return (-$pmt * (1 + $rate * $type) * (((1 + $rate) ** $nper - 1) / $rate) - $fv) / (1 + $rate) ** $nper;
        }

        return -$fv - $pmt * $nper;
    }

    /**
     * RATE.
     *
     * Returns the interest rate per period of an annuity.
     * RATE is calculated by iteration and can have zero or more solutions.
     * If the successive results of RATE do not converge to within 0.0000001 after 20 iterations,
     * RATE returns the #NUM! error value.
     *
     * Excel Function:
     *        RATE(nper,pmt,pv[,fv[,type[,guess]]])
     *
     * @param mixed (float) $nper The total number of payment periods in an annuity
     * @param mixed (float) $pmt The payment made each period and cannot change over the life
     *                                    of the annuity.
     *                                Typically, pmt includes principal and interest but no other
     *                                    fees or taxes.
     * @param mixed (float) $pv The present value - the total amount that a series of future
     *                                    payments is worth now
     * @param mixed (float) $fv The future value, or a cash balance you want to attain after
     *                                    the last payment is made. If fv is omitted, it is assumed
     *                                    to be 0 (the future value of a loan, for example, is 0).
     * @param mixed (int) $type A number 0 or 1 and indicates when payments are due:
     *                                        0 or omitted    At the end of the period.
     *                                        1                At the beginning of the period.
     * @param mixed (float) $guess Your guess for what the rate will be.
     *                                    If you omit guess, it is assumed to be 10 percent.
     *
     * @return float|string
     */
    public static function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1)
    {
        $nper = (int) Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $fv = ($fv === null) ? 0.0 : Functions::flattenSingleValue($fv);
        $type = ($type === null) ? 0 : (int) Functions::flattenSingleValue($type);
        $guess = ($guess === null) ? 0.1 : Functions::flattenSingleValue($guess);

        $rate = $guess;
        // rest of code adapted from python/numpy
        $close = false;
        $iter = 0;
        while (!$close && $iter < self::FINANCIAL_MAX_ITERATIONS) {
            $nextdiff = self::rateNextGuess($rate, $nper, $pmt, $pv, $fv, $type);
            if (!is_numeric($nextdiff)) {
                break;
            }
            $rate1 = $rate - $nextdiff;
            $close = abs($rate1 - $rate) < self::FINANCIAL_PRECISION;
            ++$iter;
            $rate = $rate1;
        }

        return $close ? $rate : Functions::NAN();
    }

    private static function rateNextGuess($rate, $nper, $pmt, $pv, $fv, $type)
    {
        if ($rate == 0) {
            return Functions::NAN();
        }
        $tt1 = ($rate + 1) ** $nper;
        $tt2 = ($rate + 1) ** ($nper - 1);
        $numerator = $fv + $tt1 * $pv + $pmt * ($tt1 - 1) * ($rate * $type + 1) / $rate;
        $denominator = $nper * $tt2 * $pv - $pmt * ($tt1 - 1) * ($rate * $type + 1) / ($rate * $rate)
             + $nper * $pmt * $tt2 * ($rate * $type + 1) / $rate
             + $pmt * ($tt1 - 1) * $type / $rate;
        if ($denominator == 0) {
            return Functions::NAN();
        }

        return $numerator / $denominator;
    }

    /**
     * RECEIVED.
     *
     * Returns the price per $100 face value of a discounted security.
     *
     * @param mixed $settlement The security's settlement date.
     *                                The security settlement date is the date after the issue date when the security is traded to the buyer.
     * @param mixed $maturity The security's maturity date.
     *                                The maturity date is the date when the security expires.
     * @param mixed (int) $investment The amount invested in the security
     * @param mixed (int) $discount The security's discount rate
     * @param mixed (int) $basis The type of day count to use.
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string Result, or a string containing an error
     */
    public static function RECEIVED($settlement, $maturity, $investment, $discount, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $investment = (float) Functions::flattenSingleValue($investment);
        $discount = (float) Functions::flattenSingleValue($discount);
        $basis = (int) Functions::flattenSingleValue($basis);

        //    Validate
        if ((is_numeric($investment)) && (is_numeric($discount)) && (is_numeric($basis))) {
            if (($investment <= 0) || ($discount <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTimeExcel\YearFrac::funcYearFrac($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                //    return date error
                return $daysBetweenSettlementAndMaturity;
            }

            return $investment / (1 - ($discount * $daysBetweenSettlementAndMaturity));
        }

        return Functions::VALUE();
    }

    /**
     * RRI.
     *
     * Calculates the interest rate required for an investment to grow to a specified future value .
     *
     * @param float $nper The number of periods over which the investment is made
     * @param float $pv Present Value
     * @param float $fv Future Value
     *
     * @return float|string Result, or a string containing an error
     */
    public static function RRI($nper = 0, $pv = 0, $fv = 0)
    {
        $nper = Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);

        // Validate parameters
        if (!is_numeric($nper) || !is_numeric($pv) || !is_numeric($fv)) {
            return Functions::VALUE();
        } elseif ($nper <= 0.0 || $pv <= 0.0 || $fv < 0.0) {
            return Functions::NAN();
        }

        return ($fv / $pv) ** (1 / $nper) - 1;
    }

    /**
     * SLN.
     *
     * Returns the straight-line depreciation of an asset for one period
     *
     * @Deprecated 1.18.0
     *
     * @see Use the SLN() method in the Financial\Depreciation class instead
     *
     * @param mixed $cost Initial cost of the asset
     * @param mixed $salvage Value at the end of the depreciation
     * @param mixed $life Number of periods over which the asset is depreciated
     *
     * @return float|string Result, or a string containing an error
     */
    public static function SLN($cost, $salvage, $life)
    {
        return Depreciation::SLN($cost, $salvage, $life);
    }

    /**
     * SYD.
     *
     * Returns the sum-of-years' digits depreciation of an asset for a specified period.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the SYD() method in the Financial\Depreciation class instead
     *
     * @param mixed $cost Initial cost of the asset
     * @param mixed $salvage Value at the end of the depreciation
     * @param mixed $life Number of periods over which the asset is depreciated
     * @param mixed $period Period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function SYD($cost, $salvage, $life, $period)
    {
        return Depreciation::SYD($cost, $salvage, $life, $period);
    }

    /**
     * TBILLEQ.
     *
     * Returns the bond-equivalent yield for a Treasury bill.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the bondEquivalentYield() method in the Financial\TreasuryBill class instead
     *
     * @param mixed $settlement The Treasury bill's settlement date.
     *                                The Treasury bill's settlement date is the date after the issue date when the Treasury bill is traded to the buyer.
     * @param mixed $maturity The Treasury bill's maturity date.
     *                                The maturity date is the date when the Treasury bill expires.
     * @param int $discount The Treasury bill's discount rate
     *
     * @return float|string Result, or a string containing an error
     */
    public static function TBILLEQ($settlement, $maturity, $discount)
    {
        return TreasuryBill::bondEquivalentYield($settlement, $maturity, $discount);
    }

    /**
     * TBILLPRICE.
     *
     * Returns the price per $100 face value for a Treasury bill.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the price() method in the Financial\TreasuryBill class instead
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
    public static function TBILLPRICE($settlement, $maturity, $discount)
    {
        return TreasuryBill::price($settlement, $maturity, $discount);
    }

    /**
     * TBILLYIELD.
     *
     * Returns the yield for a Treasury bill.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the yield() method in the Financial\TreasuryBill class instead
     *
     * @param mixed $settlement The Treasury bill's settlement date.
     *                                The Treasury bill's settlement date is the date after the issue date
     *                                    when the Treasury bill is traded to the buyer.
     * @param mixed $maturity The Treasury bill's maturity date.
     *                                The maturity date is the date when the Treasury bill expires.
     * @param int $price The Treasury bill's price per $100 face value
     *
     * @return float|mixed|string
     */
    public static function TBILLYIELD($settlement, $maturity, $price)
    {
        return TreasuryBill::yield($settlement, $maturity, $price);
    }

    private static function bothNegAndPos($neg, $pos)
    {
        return $neg && $pos;
    }

    private static function xirrPart2(&$values)
    {
        $valCount = count($values);
        $foundpos = false;
        $foundneg = false;
        for ($i = 0; $i < $valCount; ++$i) {
            $fld = $values[$i];
            if (!is_numeric($fld)) {
                return Functions::VALUE();
            } elseif ($fld > 0) {
                $foundpos = true;
            } elseif ($fld < 0) {
                $foundneg = true;
            }
        }
        if (!self::bothNegAndPos($foundneg, $foundpos)) {
            return Functions::NAN();
        }

        return '';
    }

    private static function xirrPart1(&$values, &$dates)
    {
        if ((!is_array($values)) && (!is_array($dates))) {
            return Functions::NA();
        }
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        if (count($values) != count($dates)) {
            return Functions::NAN();
        }

        $datesCount = count($dates);
        for ($i = 0; $i < $datesCount; ++$i) {
            try {
                $dates[$i] = DateTimeExcel\Helpers::getDateValue($dates[$i]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return self::xirrPart2($values);
    }

    private static function xirrPart3($values, $dates, $x1, $x2)
    {
        $f = self::xnpvOrdered($x1, $values, $dates, false);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        $rslt = Functions::VALUE();
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::xnpvOrdered($x_mid, $values, $dates, false);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                $rslt = $x_mid;

                break;
            }
        }

        return $rslt;
    }

    /**
     * XIRR.
     *
     * Returns the internal rate of return for a schedule of cash flows that is not necessarily periodic.
     *
     * Excel Function:
     *        =XIRR(values,dates,guess)
     *
     * @param float[] $values     A series of cash flow payments
     *                                The series of values must contain at least one positive value & one negative value
     * @param mixed[] $dates      A series of payment dates
     *                                The first payment date indicates the beginning of the schedule of payments
     *                                All other dates must be later than this date, but they may occur in any order
     * @param float $guess        An optional guess at the expected answer
     *
     * @return float|mixed|string
     */
    public static function XIRR($values, $dates, $guess = 0.1)
    {
        $rslt = self::xirrPart1($values, $dates);
        if ($rslt) {
            return $rslt;
        }

        // create an initial range, with a root somewhere between 0 and guess
        $guess = Functions::flattenSingleValue($guess);
        $x1 = 0.0;
        $x2 = $guess ?: 0.1;
        $f1 = self::xnpvOrdered($x1, $values, $dates, false);
        $f2 = self::xnpvOrdered($x2, $values, $dates, false);
        $found = false;
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (!is_numeric($f1) || !is_numeric($f2)) {
                break;
            }
            if (($f1 * $f2) < 0.0) {
                $found = true;

                break;
            } elseif (abs($f1) < abs($f2)) {
                $f1 = self::xnpvOrdered($x1 += 1.6 * ($x1 - $x2), $values, $dates, false);
            } else {
                $f2 = self::xnpvOrdered($x2 += 1.6 * ($x2 - $x1), $values, $dates, false);
            }
        }
        if (!$found) {
            return Functions::NAN();
        }

        return self::xirrPart3($values, $dates, $x1, $x2);
    }

    /**
     * XNPV.
     *
     * Returns the net present value for a schedule of cash flows that is not necessarily periodic.
     * To calculate the net present value for a series of cash flows that is periodic, use the NPV function.
     *
     * Excel Function:
     *        =XNPV(rate,values,dates)
     *
     * @param float $rate the discount rate to apply to the cash flows
     * @param float[] $values     A series of cash flows that corresponds to a schedule of payments in dates.
     *                                 The first payment is optional and corresponds to a cost or payment that occurs at the beginning of the investment.
     *                                 If the first value is a cost or payment, it must be a negative value. All succeeding payments are discounted based on a 365-day year.
     *                                 The series of values must contain at least one positive value and one negative value.
     * @param mixed[] $dates      A schedule of payment dates that corresponds to the cash flow payments.
     *                                 The first payment date indicates the beginning of the schedule of payments.
     *                                 All other dates must be later than this date, but they may occur in any order.
     *
     * @return float|mixed|string
     */
    public static function XNPV($rate, $values, $dates)
    {
        return self::xnpvOrdered($rate, $values, $dates, true);
    }

    private static function validateXnpv($rate, $values, $dates)
    {
        if (!is_numeric($rate)) {
            return Functions::VALUE();
        }
        $valCount = count($values);
        if ($valCount != count($dates)) {
            return Functions::NAN();
        }
        if ($valCount > 1 && ((min($values) > 0) || (max($values) < 0))) {
            return Functions::NAN();
        }
        $date0 = DateTimeExcel\Helpers::getDateValue($dates[0]);
        if (is_string($date0)) {
            return Functions::VALUE();
        }

        return '';
    }

    private static function xnpvOrdered($rate, $values, $dates, $ordered = true)
    {
        $rate = Functions::flattenSingleValue($rate);
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        $valCount = count($values);

        try {
            $date0 = DateTimeExcel\Helpers::getDateValue($dates[0]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $rslt = self::validateXnpv($rate, $values, $dates);
        if ($rslt) {
            return $rslt;
        }
        $xnpv = 0.0;
        for ($i = 0; $i < $valCount; ++$i) {
            if (!is_numeric($values[$i])) {
                return Functions::VALUE();
            }

            try {
                $datei = DateTimeExcel\Helpers::getDateValue($dates[$i]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            if ($date0 > $datei) {
                $dif = $ordered ? Functions::NAN() : -DateTimeExcel\DateDif::funcDateDif($datei, $date0, 'd');
            } else {
                $dif = DateTimeExcel\DateDif::funcDateDif($date0, $datei, 'd');
            }
            if (!is_numeric($dif)) {
                return $dif;
            }
            $xnpv += $values[$i] / (1 + $rate) ** ($dif / 365);
        }

        return is_finite($xnpv) ? $xnpv : Functions::VALUE();
    }

    /**
     * YIELDDISC.
     *
     * Returns the annual yield of a security that pays interest at maturity.
     *
     * @see Use the yieldDiscounted() method in the Financial\Securities\Yields class instead
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
    public static function YIELDDISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        return Securities\Yields::yieldDiscounted($settlement, $maturity, $price, $redemption, $basis);
    }

    /**
     * YIELDMAT.
     *
     * Returns the annual yield of a security that pays interest at maturity.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the yieldAtMaturity() method in the Financial\Securities\Yields class instead
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
    public static function YIELDMAT($settlement, $maturity, $issue, $rate, $price, $basis = 0)
    {
        return Securities\Yields::yieldAtMaturity($settlement, $maturity, $issue, $rate, $price, $basis);
    }
}
