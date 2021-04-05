<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Cumulative
{
    use Financial\BaseValidations;

    /**
     * CUMIPMT.
     *
     * Returns the cumulative interest paid on a loan between the start and end periods.
     *
     * Excel Function:
     *        CUMIPMT(rate,nper,pv,start,end[,type])
     *
     * @param float $rate The Interest rate
     * @param int $periods The total number of payment periods
     * @param float $presentValue Present Value
     * @param int $start The first period in the calculation.
     *                       Payment periods are numbered beginning with 1.
     * @param int $end the last period in the calculation
     * @param int $type A number 0 or 1 and indicates when payments are due:
     *                    0 or omitted    At the end of the period.
     *                    1               At the beginning of the period.
     *
     * @return float|string
     */
    public static function CUMIPMT($rate, $periods, $presentValue, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $periods = Functions::flattenSingleValue($periods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $start = Functions::flattenSingleValue($start);
        $end = Functions::flattenSingleValue($end);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $rate = self::validateFloat($rate);
            $periods = self::validateInt($periods);
            $presentValue = self::validateFloat($presentValue);
            $start = self::validateInt($start);
            $end = self::validateInt($end);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($type !== 0 && $type !== 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        // Calculate
        $interest = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ipmt = Financial::IPMT($rate, $per, $periods, $presentValue, 0, $type);
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
     * @param int $periods The total number of payment periods
     * @param float $presentValue Present Value
     * @param int $start The first period in the calculation.
     *                       Payment periods are numbered beginning with 1.
     * @param int $end the last period in the calculation
     * @param int $type A number 0 or 1 and indicates when payments are due:
     *                    0 or omitted    At the end of the period.
     *                    1               At the beginning of the period.
     *
     * @return float|string
     */
    public static function CUMPRINC($rate, $periods, $presentValue, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $periods = Functions::flattenSingleValue($periods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $start = Functions::flattenSingleValue($start);
        $end = Functions::flattenSingleValue($end);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $rate = self::validateFloat($rate);
            $periods = self::validateInt($periods);
            $presentValue = self::validateFloat($presentValue);
            $start = self::validateInt($start);
            $end = self::validateInt($end);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($type !== 0 && $type !== 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        // Calculate
        $principal = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $ppmt = Financial::PPMT($rate, $per, $periods, $presentValue, 0, $type);
            if (is_string($ppmt)) {
                return $ppmt;
            }

            $principal += $ppmt;
        }

        return $principal;
    }
}
