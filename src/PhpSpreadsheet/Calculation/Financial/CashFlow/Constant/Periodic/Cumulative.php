<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\CashFlowValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Cumulative
{
    /**
     * CUMIPMT.
     *
     * Returns the cumulative interest paid on a loan between the start and end periods.
     *
     * Excel Function:
     *        CUMIPMT(rate,nper,pv,start,end[,type])
     *
     * @param mixed $rate The Interest rate
     * @param mixed $periods The total number of payment periods
     * @param mixed $presentValue Present Value
     * @param mixed $start The first period in the calculation.
     *                       Payment periods are numbered beginning with 1.
     * @param mixed $end the last period in the calculation
     * @param mixed $type A number 0 or 1 and indicates when payments are due:
     *                    0 or omitted    At the end of the period.
     *                    1               At the beginning of the period.
     *
     * @return float|string
     */
    public static function interest(
        mixed $rate,
        mixed $periods,
        mixed $presentValue,
        mixed $start,
        mixed $end,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ): string|float|int {
        $rate = Functions::flattenSingleValue($rate);
        $periods = Functions::flattenSingleValue($periods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $start = Functions::flattenSingleValue($start);
        $end = Functions::flattenSingleValue($end);
        $type = ($type === null) ? FinancialConstants::PAYMENT_END_OF_PERIOD : Functions::flattenSingleValue($type);

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $periods = CashFlowValidations::validateInt($periods);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $start = CashFlowValidations::validateInt($start);
            $end = CashFlowValidations::validateInt($end);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($start < 1 || $start > $end) {
            return ExcelError::NAN();
        }

        // Calculate
        $interest = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ipmt = Interest::payment($rate, $per, $periods, $presentValue, 0, $type);
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
     * @param mixed $rate The Interest rate
     * @param mixed $periods The total number of payment periods as an integer
     * @param mixed $presentValue Present Value
     * @param mixed $start The first period in the calculation.
     *                       Payment periods are numbered beginning with 1.
     * @param mixed $end the last period in the calculation
     * @param mixed $type A number 0 or 1 and indicates when payments are due:
     *                    0 or omitted    At the end of the period.
     *                    1               At the beginning of the period.
     *
     * @return float|string
     */
    public static function principal(
        mixed $rate,
        mixed $periods,
        mixed $presentValue,
        mixed $start,
        mixed $end,
        mixed $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ): string|float|int {
        $rate = Functions::flattenSingleValue($rate);
        $periods = Functions::flattenSingleValue($periods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $start = Functions::flattenSingleValue($start);
        $end = Functions::flattenSingleValue($end);
        $type = ($type === null) ? FinancialConstants::PAYMENT_END_OF_PERIOD : Functions::flattenSingleValue($type);

        try {
            $rate = CashFlowValidations::validateRate($rate);
            $periods = CashFlowValidations::validateInt($periods);
            $presentValue = CashFlowValidations::validatePresentValue($presentValue);
            $start = CashFlowValidations::validateInt($start);
            $end = CashFlowValidations::validateInt($end);
            $type = CashFlowValidations::validatePeriodType($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($start < 1 || $start > $end) {
            return ExcelError::VALUE();
        }

        // Calculate
        $principal = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ppmt = Payments::interestPayment($rate, $per, $periods, $presentValue, 0, $type);
            if (is_string($ppmt)) {
                return $ppmt;
            }

            $principal += $ppmt;
        }

        return $principal;
    }
}
