<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Depreciation
{
    /** @var float */
    private static $zeroPointZero = 0.0;

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
     * @param mixed $cost Initial cost of the asset
     * @param mixed $salvage Value at the end of the depreciation.
     *                             (Sometimes called the salvage value of the asset)
     * @param mixed $life Number of periods over which the asset is depreciated.
     *                           (Sometimes called the useful life of the asset)
     * @param mixed $period The period for which you want to calculate the
     *                          depreciation. Period must use the same units as life.
     * @param mixed $month Number of months in the first year. If month is omitted,
     *                         it defaults to 12.
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
            $month = self::validateMonth($month);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($cost === self::$zeroPointZero) {
            return 0.0;
        }

        //    Set Fixed Depreciation Rate
        $fixedDepreciationRate = 1 - ($salvage / $cost) ** (1 / $life);
        $fixedDepreciationRate = round($fixedDepreciationRate, 3);

        //    Loop through each period calculating the depreciation
        // TODO Handle period value between 0 and 1 (e.g. 0.5)
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

    /**
     * DDB.
     *
     * Returns the depreciation of an asset for a specified period using the
     * double-declining balance method or some other method you specify.
     *
     * Excel Function:
     *        DDB(cost,salvage,life,period[,factor])
     *
     * @param mixed $cost Initial cost of the asset
     * @param mixed $salvage Value at the end of the depreciation.
     *                                (Sometimes called the salvage value of the asset)
     * @param mixed $life Number of periods over which the asset is depreciated.
     *                                (Sometimes called the useful life of the asset)
     * @param mixed $period The period for which you want to calculate the
     *                                depreciation. Period must use the same units as life.
     * @param mixed $factor The rate at which the balance declines.
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
            $factor = self::validateFactor($factor);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($period > $life) {
            return ExcelError::NAN();
        }

        // Loop through each period calculating the depreciation
        // TODO Handling for fractional $period values
        $previousDepreciation = 0;
        $depreciation = 0;
        for ($per = 1; $per <= $period; ++$per) {
            $depreciation = min(
                ($cost - $previousDepreciation) * ($factor / $life),
                ($cost - $salvage - $previousDepreciation)
            );
            $previousDepreciation += $depreciation;
        }

        return $depreciation;
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
            $cost = self::validateCost($cost, true);
            $salvage = self::validateSalvage($salvage, true);
            $life = self::validateLife($life, true);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($life === self::$zeroPointZero) {
            return ExcelError::DIV0();
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
            $cost = self::validateCost($cost, true);
            $salvage = self::validateSalvage($salvage);
            $life = self::validateLife($life);
            $period = self::validatePeriod($period);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($period > $life) {
            return ExcelError::NAN();
        }

        $syd = (($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1));

        return $syd;
    }

    /** @param mixed $cost */
    private static function validateCost($cost, bool $negativeValueAllowed = false): float
    {
        $cost = FinancialValidations::validateFloat($cost);
        if ($cost < 0.0 && $negativeValueAllowed === false) {
            throw new Exception(ExcelError::NAN());
        }

        return $cost;
    }

    /** @param mixed $salvage */
    private static function validateSalvage($salvage, bool $negativeValueAllowed = false): float
    {
        $salvage = FinancialValidations::validateFloat($salvage);
        if ($salvage < 0.0 && $negativeValueAllowed === false) {
            throw new Exception(ExcelError::NAN());
        }

        return $salvage;
    }

    /** @param mixed $life */
    private static function validateLife($life, bool $negativeValueAllowed = false): float
    {
        $life = FinancialValidations::validateFloat($life);
        if ($life < 0.0 && $negativeValueAllowed === false) {
            throw new Exception(ExcelError::NAN());
        }

        return $life;
    }

    /** @param mixed $period */
    private static function validatePeriod($period, bool $negativeValueAllowed = false): float
    {
        $period = FinancialValidations::validateFloat($period);
        if ($period <= 0.0 && $negativeValueAllowed === false) {
            throw new Exception(ExcelError::NAN());
        }

        return $period;
    }

    /** @param mixed $month */
    private static function validateMonth($month): int
    {
        $month = FinancialValidations::validateInt($month);
        if ($month < 1) {
            throw new Exception(ExcelError::NAN());
        }

        return $month;
    }

    /** @param mixed $factor */
    private static function validateFactor($factor): float
    {
        $factor = FinancialValidations::validateFloat($factor);
        if ($factor <= 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $factor;
    }
}
