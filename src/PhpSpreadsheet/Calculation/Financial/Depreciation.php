<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Depreciation
{
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
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);
        $month = Functions::flattenSingleValue($month);

        //    Validate
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($month))) {
            $cost = (float) $cost;
            $salvage = (float) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $month = (int) $month;
            if ($cost == 0) {
                return 0.0;
            } elseif (($cost < 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($month < 1)) {
                return Functions::NAN();
            }
            //    Set Fixed Depreciation Rate
            $fixedDepreciationRate = 1 - ($salvage / $cost) ** (1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);

            //    Loop through each period calculating the depreciation
            $previousDepreciation = 0;
            $depreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                if ($per == 1) {
                    $depreciation = $cost * $fixedDepreciationRate * $month / 12;
                } elseif ($per == ($life + 1)) {
                    $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate * (12 - $month) / 12;
                } else {
                    $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate;
                }
                $previousDepreciation += $depreciation;
            }

            return $depreciation;
        }

        return Functions::VALUE();
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
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);
        $factor = Functions::flattenSingleValue($factor);

        //    Validate
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($factor))) {
            $cost = (float) $cost;
            $salvage = (float) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $factor = (float) $factor;
            if (($cost <= 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($factor <= 0.0) || ($period > $life)) {
                return Functions::NAN();
            }
            //    Set Fixed Depreciation Rate
            $fixedDepreciationRate = 1 - ($salvage / $cost) ** (1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);

            //    Loop through each period calculating the depreciation
            $previousDepreciation = 0;
            $depreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                $depreciation = min(($cost - $previousDepreciation) * ($factor / $life), ($cost - $salvage - $previousDepreciation));
                $previousDepreciation += $depreciation;
            }

            return $depreciation;
        }

        return Functions::VALUE();
    }

    /**
     * SLN.
     *
     * Returns the straight-line depreciation of an asset for one period
     *
     * @param mixed $cost Initial cost of the asset
     * @param mixed $salvage Value at the end of the depreciation
     * @param mixed $life Number of periods over which the asset is depreciated
     *
     * @return float|string Result, or a string containing an error
     */
    public static function SLN($cost, $salvage, $life)
    {
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);

        // Calculate
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life))) {
            if ($life < 0) {
                return Functions::NAN();
            }

            return ($cost - $salvage) / $life;
        }

        return Functions::VALUE();
    }

    /**
     * SYD.
     *
     * Returns the sum-of-years' digits depreciation of an asset for a specified period.
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
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);

        // Calculate
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period))) {
            if (($life < 1) || ($period > $life)) {
                return Functions::NAN();
            }

            return (($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1));
        }

        return Functions::VALUE();
    }
}
