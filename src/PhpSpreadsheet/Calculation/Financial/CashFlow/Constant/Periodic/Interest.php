<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\CashFlowValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Interest
{
    private const FINANCIAL_MAX_ITERATIONS = 128;

    private const FINANCIAL_PRECISION = 1.0e-08;

    /**
     * IPMT.
     *
     * Returns the interest payment for a given period for an investment based on periodic, constant payments
     *         and a constant interest rate.
     *
     * Excel Function:
     *        IPMT(rate,per,nper,pv[,fv][,type])
     *
     * @param mixed $interestRate Interest rate per period
     * @param mixed $period Period for which we want to find the interest
     * @param mixed $numberOfPeriods Number of periods
     * @param mixed $presentValue Present Value
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     */
    public static function payment(
        mixed $interestRate,
        mixed $period,
        mixed $numberOfPeriods,
        mixed $presentValue,
        mixed $futureValue = 0,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ): string|float {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $period = Functions::flattenSingleValue($period);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = ($futureValue === null) ? 0.0 : Functions::flattenSingleValue($futureValue);
        $type = ($type === null) ? FinancialConstants::PAYMENT_END_OF_PERIOD : Functions::flattenSingleValue($type);

        try {
            $interestRate = CashFlowValidations::validateRate($interestRate);
            $period = CashFlowValidations::validateInt($period);
            $numberOfPeriods = CashFlowValidations::validateInt($numberOfPeriods);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($period <= 0 || $period > $numberOfPeriods) {
            return ExcelError::NAN();
        }

        // Calculate
        $interestAndPrincipal = new InterestAndPrincipal(
            $interestRate,
            $period,
            $numberOfPeriods,
            $presentValue,
            $futureValue,
            $type
        );

        return $interestAndPrincipal->interest();
    }

    /**
     * ISPMT.
     *
     * Returns the interest payment for an investment based on an interest rate and a constant payment schedule.
     *
     * Excel Function:
     *     =ISPMT(interest_rate, period, number_payments, pv)
     *
     * @param mixed $interestRate is the interest rate for the investment
     * @param mixed $period is the period to calculate the interest rate.  It must be betweeen 1 and number_payments.
     * @param mixed $numberOfPeriods is the number of payments for the annuity
     * @param mixed $principleRemaining is the loan amount or present value of the payments
     */
    public static function schedulePayment(mixed $interestRate, mixed $period, mixed $numberOfPeriods, mixed $principleRemaining): string|float
    {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $period = Functions::flattenSingleValue($period);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $principleRemaining = Functions::flattenSingleValue($principleRemaining);

        try {
            $interestRate = CashFlowValidations::validateRate($interestRate);
            $period = CashFlowValidations::validateInt($period);
            $numberOfPeriods = CashFlowValidations::validateInt($numberOfPeriods);
            $principleRemaining = CashFlowValidations::validateFloat($principleRemaining);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($period <= 0 || $period > $numberOfPeriods) {
            return ExcelError::NAN();
        }

        // Return value
        $returnValue = 0;

        // Calculate
        $principlePayment = ($principleRemaining * 1.0) / ($numberOfPeriods * 1.0);
        for ($i = 0; $i <= $period; ++$i) {
            $returnValue = $interestRate * $principleRemaining * -1;
            $principleRemaining -= $principlePayment;
            // principle needs to be 0 after the last payment, don't let floating point screw it up
            if ($i == $numberOfPeriods) {
                $returnValue = 0.0;
            }
        }

        return $returnValue;
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
     * @param mixed $numberOfPeriods The total number of payment periods in an annuity
     * @param mixed $payment The payment made each period and cannot change over the life of the annuity.
     *                           Typically, pmt includes principal and interest but no other fees or taxes.
     * @param mixed $presentValue The present value - the total amount that a series of future payments is worth now
     * @param mixed $futureValue The future value, or a cash balance you want to attain after the last payment is made.
     *                               If fv is omitted, it is assumed to be 0 (the future value of a loan,
     *                               for example, is 0).
     * @param mixed $type A number 0 or 1 and indicates when payments are due:
     *                      0 or omitted    At the end of the period.
     *                      1               At the beginning of the period.
     * @param mixed $guess Your guess for what the rate will be.
     *                          If you omit guess, it is assumed to be 10 percent.
     */
    public static function rate(
        mixed $numberOfPeriods,
        mixed $payment,
        mixed $presentValue,
        mixed $futureValue = 0.0,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD,
        mixed $guess = 0.1
    ): string|float {
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $payment = Functions::flattenSingleValue($payment);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = ($futureValue === null) ? 0.0 : Functions::flattenSingleValue($futureValue);
        $type = ($type === null) ? FinancialConstants::PAYMENT_END_OF_PERIOD : Functions::flattenSingleValue($type);
        $guess = ($guess === null) ? 0.1 : Functions::flattenSingleValue($guess);

        try {
            $numberOfPeriods = CashFlowValidations::validateInt($numberOfPeriods);
            $payment = CashFlowValidations::validateFloat($payment);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
            $type = CashFlowValidations::validatePeriodType($type);
            $guess = CashFlowValidations::validateFloat($guess);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $rate = $guess;
        // rest of code adapted from python/numpy
        $close = false;
        $iter = 0;
        while (!$close && $iter < self::FINANCIAL_MAX_ITERATIONS) {
            $nextdiff = self::rateNextGuess($rate, $numberOfPeriods, $payment, $presentValue, $futureValue, $type);
            if (!is_numeric($nextdiff)) {
                break;
            }
            $rate1 = $rate - $nextdiff;
            $close = abs($rate1 - $rate) < self::FINANCIAL_PRECISION;
            ++$iter;
            $rate = $rate1;
        }

        return $close ? $rate : ExcelError::NAN();
    }

    private static function rateNextGuess(float $rate, int $numberOfPeriods, float $payment, float $presentValue, float $futureValue, int $type): string|float
    {
        if ($rate == 0.0) {
            return ExcelError::NAN();
        }
        $tt1 = ($rate + 1) ** $numberOfPeriods;
        $tt2 = ($rate + 1) ** ($numberOfPeriods - 1);
        $numerator = $futureValue + $tt1 * $presentValue + $payment * ($tt1 - 1) * ($rate * $type + 1) / $rate;
        $denominator = $numberOfPeriods * $tt2 * $presentValue - $payment * ($tt1 - 1)
            * ($rate * $type + 1) / ($rate * $rate) + $numberOfPeriods
            * $payment * $tt2 * ($rate * $type + 1) / $rate + $payment * ($tt1 - 1) * $type / $rate;
        if ($denominator == 0) {
            return ExcelError::NAN();
        }

        return $numerator / $denominator;
    }
}
