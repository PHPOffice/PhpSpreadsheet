<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
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

        try {
            $cost = self::validateCost($cost);
            $salvage = self::validateSalvage($salvage);
            $life = self::validateLife($life);
            $period = self::validatePeriod($period);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        //    Validate
        if (is_numeric($month)) {
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

        try {
            $cost = self::validateCost($cost);
            $salvage = self::validateSalvage($salvage);
            $life = self::validateLife($life);
            $period = self::validatePeriod($period);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (is_numeric($factor)) {
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

        try {
            $cost = self::validateCost($cost);
            $salvage = self::validateSalvage($salvage);
            $life = self::validateLife($life);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Calculate
        if ($life < 0) {
            return Functions::NAN();
        }

        return ($cost - $salvage) / $life;
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

        try {
            $cost = self::validateCost($cost);
            $salvage = self::validateSalvage($salvage);
            $life = self::validateLife($life);
            $period = self::validatePeriod($period);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Calculate
        if (($life < 1) || ($period > $life)) {
            return Functions::NAN();
        }

        return (($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1));
    }

    private static function validateCost($cost)
    {
        if (!is_numeric($cost)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $cost;
    }

    private static function validateSalvage($salvage)
    {
        if (!is_numeric($salvage)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $salvage;
    }

    private static function validateLife($life)
    {
        if (!is_numeric($life)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) $life;
    }

    private static function validatePeriod($period)
    {
        if (!is_numeric($period)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) $period;
    }
}
