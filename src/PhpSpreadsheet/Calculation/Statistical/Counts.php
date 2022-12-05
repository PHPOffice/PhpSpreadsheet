<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Counts extends AggregateBase
{
    /**
     * COUNT.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return int
     */
    public static function COUNT(...$args)
    {
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            $arg = self::testAcceptedBoolean($arg, $k);
            // Is it a numeric value?
            // Strings containing numeric values are only counted if they are string literals (not cell values)
            //    and then only in MS Excel and in Open Office, not in Gnumeric
            if (self::isAcceptedCountable($arg, $k, true)) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    /**
     * COUNTA.
     *
     * Counts the number of cells that are not empty within the list of arguments
     *
     * Excel Function:
     *        COUNTA(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return int
     */
    public static function COUNTA(...$args)
    {
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            // Nulls are counted if literals, but not if cell values
            if ($arg !== null || (!Functions::isCellValue($k))) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    /**
     * COUNTBLANK.
     *
     * Counts the number of empty cells within the list of arguments
     *
     * Excel Function:
     *        COUNTBLANK(value1[,value2[, ...]])
     *
     * @param mixed $range Data values
     *
     * @return int
     */
    public static function COUNTBLANK($range)
    {
        if ($range === null) {
            return 1;
        }
        if (!is_array($range) || array_key_exists(0, $range)) {
            throw new CalcException('Must specify range of cells, not any kind of literal');
        }
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArray($range);
        foreach ($aArgs as $arg) {
            // Is it a blank cell?
            if (($arg === null) || ((is_string($arg)) && ($arg == ''))) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }
}
