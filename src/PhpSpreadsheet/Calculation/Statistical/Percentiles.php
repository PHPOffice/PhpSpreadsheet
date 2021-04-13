<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Percentiles
{
    public const RANK_SORT_DESCENDING = 0;

    public const RANK_SORT_ASCENDING = 1;

    /**
     * PERCENTILE.
     *
     * Returns the nth percentile of values in a range..
     *
     * Excel Function:
     *        PERCENTILE(value1[,value2[, ...]],entry)
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function PERCENTILE(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $entry = array_pop($aArgs);

        try {
            $entry = StatisticalValidations::validateFloat($entry);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($entry < 0) || ($entry > 1)) {
            return Functions::NAN();
        }

        $mArgs = self::percentileFilterValues($aArgs);
        $mValueCount = count($mArgs);
        if ($mValueCount > 0) {
            sort($mArgs);
            $count = Counts::COUNT($mArgs);
            $index = $entry * ($count - 1);
            $iBase = floor($index);
            if ($index == $iBase) {
                return $mArgs[$index];
            }
            $iNext = $iBase + 1;
            $iProportion = $index - $iBase;

            return $mArgs[$iBase] + (($mArgs[$iNext] - $mArgs[$iBase]) * $iProportion);
        }

        return Functions::NAN();
    }

    /**
     * PERCENTRANK.
     *
     * Returns the rank of a value in a data set as a percentage of the data set.
     * Note that the returned rank is simply rounded to the appropriate significant digits,
     *      rather than floored (as MS Excel), so value 3 for a value set of  1, 2, 3, 4 will return
     *      0.667 rather than 0.666
     *
     * @param mixed $valueSet An array of (float) values, or a reference to, a list of numbers
     * @param mixed $value The number whose rank you want to find
     * @param mixed $significance The (integer) number of significant digits for the returned percentage value
     *
     * @return float|string (string if result is an error)
     */
    public static function PERCENTRANK($valueSet, $value, $significance = 3)
    {
        $valueSet = Functions::flattenArray($valueSet);
        $value = Functions::flattenSingleValue($value);
        $significance = ($significance === null) ? 3 : Functions::flattenSingleValue($significance);

        try {
            $value = StatisticalValidations::validateFloat($value);
            $significance = StatisticalValidations::validateInt($significance);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $valueSet = self::rankFilterValues($valueSet);
        $valueCount = count($valueSet);
        if ($valueCount == 0) {
            return Functions::NA();
        }
        sort($valueSet, SORT_NUMERIC);

        $valueAdjustor = $valueCount - 1;
        if (($value < $valueSet[0]) || ($value > $valueSet[$valueAdjustor])) {
            return Functions::NA();
        }

        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            $pos = 0;
            $testValue = $valueSet[0];
            while ($testValue < $value) {
                $testValue = $valueSet[++$pos];
            }
            --$pos;
            $pos += (($value - $valueSet[$pos]) / ($testValue - $valueSet[$pos]));
        }

        return round($pos / $valueAdjustor, $significance);
    }

    /**
     * QUARTILE.
     *
     * Returns the quartile of a data set.
     *
     * Excel Function:
     *        QUARTILE(value1[,value2[, ...]],entry)
     *
     * @param mixed $args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function QUARTILE(...$args)
    {
        $aArgs = Functions::flattenArray($args);
        $entry = array_pop($aArgs);

        try {
            $entry = StatisticalValidations::validateFloat($entry);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $entry = floor($entry);
        $entry /= 4;
        if (($entry < 0) || ($entry > 1)) {
            return Functions::NAN();
        }

        return self::PERCENTILE($aArgs, $entry);
    }

    /**
     * RANK.
     *
     * Returns the rank of a number in a list of numbers.
     *
     * @param mixed $value The number whose rank you want to find
     * @param mixed $valueSet An array of float values, or a reference to, a list of numbers
     * @param mixed $order Order to sort the values in the value set
     *
     * @return float|string The result, or a string containing an error (0 = Descending, 1 = Ascending)
     */
    public static function RANK($value, $valueSet, $order = self::RANK_SORT_DESCENDING)
    {
        $value = Functions::flattenSingleValue($value);
        $valueSet = Functions::flattenArray($valueSet);
        $order = ($order === null) ? self::RANK_SORT_DESCENDING : Functions::flattenSingleValue($order);

        try {
            $value = StatisticalValidations::validateFloat($value);
            $order = StatisticalValidations::validateInt($order);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $valueSet = self::rankFilterValues($valueSet);
        if ($order === self::RANK_SORT_DESCENDING) {
            rsort($valueSet, SORT_NUMERIC);
        } else {
            sort($valueSet, SORT_NUMERIC);
        }

        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            return Functions::NA();
        }

        return ++$pos;
    }

    protected static function percentileFilterValues(array $dataSet)
    {
        return array_filter(
            $dataSet,
            function ($value): bool {
                return is_numeric($value) && !is_string($value);
            }
        );
    }

    protected static function rankFilterValues(array $dataSet)
    {
        return array_filter(
            $dataSet,
            function ($value): bool {
                return is_numeric($value);
            }
        );
    }
}
