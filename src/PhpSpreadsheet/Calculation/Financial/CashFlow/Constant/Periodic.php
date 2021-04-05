<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\BaseValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Periodic
{
    use BaseValidations;

    /**
     * FV.
     *
     * Returns the Future Value of a cash flow with constant payments and interest rate (annuities).
     *
     * Excel Function:
     *        FV(rate,nper,pmt[,pv[,type]])
     *
     * @param mixed $rate The interest rate per period
     * @param mixed $periods Total number of payment periods in an annuity as an integer
     * @param mixed $payment The payment made each period: it cannot change over the
     *                            life of the annuity. Typically, pmt contains principal
     *                            and interest but no other fees or taxes.
     * @param mixed $presentValue present Value, or the lump-sum amount that a series of
     *                            future payments is worth right now
     * @param mixed $type A number 0 or 1 and indicates when payments are due:
     *                                0 or omitted    At the end of the period.
     *                                1                At the beginning of the period.
     *
     * @return float|string
     */
    public static function futureValue($rate = 0, $periods = 0, $payment = 0, $presentValue = 0, $type = 0)
    {
        $rate = ($rate === null) ? 0.0 : Functions::flattenSingleValue($rate);
        $periods = ($periods === null) ? 0.0 : Functions::flattenSingleValue($periods);
        $payment = ($payment === null) ? 0.0 : Functions::flattenSingleValue($payment);
        $presentValue = ($presentValue === null) ? 0.0 : Functions::flattenSingleValue($presentValue);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $rate = self::validateFloat($rate);
            $periods = self::validateInt($periods);
            $payment = self::validateFloat($payment);
            $presentValue = self::validateFloat($presentValue);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($periods < 0 || ($type !== 0 && $type !== 1)) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            return -$presentValue *
                (1 + $rate) ** $periods - $payment * (1 + $rate * $type) * ((1 + $rate) ** $periods - 1) / $rate;
        }

        return -$presentValue - $payment * $periods;
    }

    /**
     * PV.
     *
     * Returns the Present Value of a cash flow with constant payments and interest rate (annuities).
     *
     * @param mixed $rate Interest rate per period
     * @param mixed $periods Number of periods as an integer
     * @param mixed $payment Periodic payment (annuity)
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function presentValue($rate = 0, $periods = 0, $payment = 0, $futureValue = 0, $type = 0)
    {
        $rate = ($rate === null) ? 0.0 : Functions::flattenSingleValue($rate);
        $periods = ($periods === null) ? 0.0 : Functions::flattenSingleValue($periods);
        $payment = ($payment === null) ? 0.0 : Functions::flattenSingleValue($payment);
        $futureValue = ($futureValue === null) ? 0.0 : Functions::flattenSingleValue($futureValue);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $rate = self::validateFloat($rate);
            $periods = self::validateInt($periods);
            $payment = self::validateFloat($payment);
            $futureValue = self::validateFloat($futureValue);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($periods < 0 || ($type !== 0 && $type !== 1)) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== null && $rate != 0) {
            return (-$payment * (1 + $rate * $type)
                    * (((1 + $rate) ** $periods - 1) / $rate) - $futureValue) / (1 + $rate) ** $periods;
        }

        return -$futureValue - $payment * $periods;
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
    public static function periods($rate = 0, $payment = 0, $presentValue = 0, $futureValue = 0, $type = 0)
    {
        $rate = ($rate === null) ? 0.0 : Functions::flattenSingleValue($rate);
        $payment = ($payment === null) ? 0.0 : Functions::flattenSingleValue($payment);
        $presentValue = ($presentValue === null) ? 0.0 : Functions::flattenSingleValue($presentValue);
        $futureValue = ($futureValue === null) ? 0.0 : Functions::flattenSingleValue($futureValue);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $rate = self::validateFloat($rate);
            $payment = self::validateFloat($payment);
            $presentValue = self::validateFloat($presentValue);
            $futureValue = self::validateFloat($futureValue);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($type !== 0 && $type !== 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($rate !== 0.0) {
            if ($payment === 0.0 && $presentValue === 0.0) {
                return Functions::NAN();
            }

            return log(($payment * (1 + $rate * $type) / $rate - $futureValue) /
                    ($presentValue + $payment * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        }

        if ($payment === 0.0) {
            return Functions::NAN();
        }

        return (-$presentValue - $futureValue) / $payment;
    }
}
