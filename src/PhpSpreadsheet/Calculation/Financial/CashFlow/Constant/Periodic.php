<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\CashFlowValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Periodic
{
    /**
     * FV.
     *
     * Returns the Future Value of a cash flow with constant payments and interest rate (annuities).
     *
     * Excel Function:
     *        FV(rate,nper,pmt[,pv[,type]])
     *
     * @param mixed $rate The interest rate per period
     * @param mixed $numberOfPeriods Total number of payment periods in an annuity as an integer
     * @param mixed $payment The payment made each period: it cannot change over the
     *                            life of the annuity. Typically, pmt contains principal
     *                            and interest but no other fees or taxes.
     * @param mixed $presentValue present Value, or the lump-sum amount that a series of
     *                            future payments is worth right now
     * @param mixed $type A number 0 or 1 and indicates when payments are due:
     *                      0 or omitted    At the end of the period.
     *                      1               At the beginning of the period.
     */
    public static function futureValue(
        mixed $rate,
        mixed $numberOfPeriods,
        mixed $payment = 0.0,
        mixed $presentValue = 0.0,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ): string|float {
        $rate = Functions::flattenSingleValue($rate);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $payment = Functions::flattenSingleValue($payment) ?? 0.0;
        $presentValue = Functions::flattenSingleValue($presentValue) ?? 0.0;
        $type = Functions::flattenSingleValue($type) ?? FinancialConstants::PAYMENT_END_OF_PERIOD;

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $numberOfPeriods = CashFlowValidations::validateInt($numberOfPeriods);
            $payment = CashFlowValidations::validateFloat($payment);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::calculateFutureValue($rate, $numberOfPeriods, $payment, $presentValue, $type);
    }

    /**
     * PV.
     *
     * Returns the Present Value of a cash flow with constant payments and interest rate (annuities).
     *
     * @param mixed $rate Interest rate per period
     * @param mixed $numberOfPeriods Number of periods as an integer
     * @param mixed $payment Periodic payment (annuity)
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function presentValue(
        mixed $rate,
        mixed $numberOfPeriods,
        mixed $payment = 0.0,
        mixed $futureValue = 0.0,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ): string|float {
        $rate = Functions::flattenSingleValue($rate);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $payment = Functions::flattenSingleValue($payment) ?? 0.0;
        $futureValue = Functions::flattenSingleValue($futureValue) ?? 0.0;
        $type = Functions::flattenSingleValue($type) ?? FinancialConstants::PAYMENT_END_OF_PERIOD;

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $numberOfPeriods = CashFlowValidations::validateInt($numberOfPeriods);
            $payment = CashFlowValidations::validateFloat($payment);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($numberOfPeriods < 0) {
            return ExcelError::NAN();
        }

        return self::calculatePresentValue($rate, $numberOfPeriods, $payment, $futureValue, $type);
    }

    /**
     * NPER.
     *
     * Returns the number of periods for a cash flow with constant periodic payments (annuities), and interest rate.
     *
     * @param mixed $rate Interest rate per period
     * @param mixed $payment Periodic payment (annuity)
     * @param mixed $presentValue Present Value
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function periods(
        mixed $rate,
        mixed $payment,
        mixed $presentValue,
        mixed $futureValue = 0.0,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ) {
        $rate = Functions::flattenSingleValue($rate);
        $payment = Functions::flattenSingleValue($payment);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue) ?? 0.0;
        $type = Functions::flattenSingleValue($type) ?? FinancialConstants::PAYMENT_END_OF_PERIOD;

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $payment = CashFlowValidations::validateFloat($payment);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $futureValue = CashFlowValidations::validateFutureValue($futureValue);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($payment == 0.0) {
            return ExcelError::NAN();
        }

        return self::calculatePeriods($rate, $payment, $presentValue, $futureValue, $type);
    }

    private static function calculateFutureValue(
        float $rate,
        int $numberOfPeriods,
        float $payment,
        float $presentValue,
        int $type
    ): float {
        if ($rate != 0) {
            return -$presentValue
                * (1 + $rate) ** $numberOfPeriods - $payment * (1 + $rate * $type) * ((1 + $rate) ** $numberOfPeriods - 1)
                    / $rate;
        }

        return -$presentValue - $payment * $numberOfPeriods;
    }

    private static function calculatePresentValue(
        float $rate,
        int $numberOfPeriods,
        float $payment,
        float $futureValue,
        int $type
    ): float {
        if ($rate != 0.0) {
            return (-$payment * (1 + $rate * $type)
                    * (((1 + $rate) ** $numberOfPeriods - 1) / $rate) - $futureValue) / (1 + $rate) ** $numberOfPeriods;
        }

        return -$futureValue - $payment * $numberOfPeriods;
    }

    private static function calculatePeriods(
        float $rate,
        float $payment,
        float $presentValue,
        float $futureValue,
        int $type
    ): string|float {
        if ($rate != 0.0) {
            if ($presentValue == 0.0) {
                return ExcelError::NAN();
            }

            return log(($payment * (1 + $rate * $type) / $rate - $futureValue)
                    / ($presentValue + $payment * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        }

        return (-$presentValue - $futureValue) / $payment;
    }
}
