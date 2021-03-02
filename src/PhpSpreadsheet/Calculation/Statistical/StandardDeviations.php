<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class StandardDeviations extends VarianceBase
{
    /**
     * STDEV.
     *
     * Estimates standard deviation based on a sample. The standard deviation is a measure of how
     *        widely values are dispersed from the average value (the mean).
     *
     * Excel Function:
     *        STDEV(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function STDEV(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $aMean = Averages::AVERAGE($aArgs);

        if (!is_string($aMean)) {
            $returnValue = 0.0;
            $aCount = -1;

            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) || (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $returnValue += ($arg - $aMean) ** 2;
                    ++$aCount;
                }
            }

            if ($aCount > 0) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    /**
     * STDEVA.
     *
     * Estimates standard deviation based on a sample, including numbers, text, and logical values
     *
     * Excel Function:
     *        STDEVA(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVA(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $aMean = Averages::AVERAGEA($aArgs);

        if (!is_string($aMean)) {
            $returnValue = 0.0;
            $aCount = -1;

            foreach ($aArgs as $k => $arg) {
                if ((is_bool($arg)) && (!Functions::isMatrixValue($k))) {
                } else {
                    // Is it a numeric value?
                    if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) && ($arg != '')))) {
                        $arg = self::datatypeAdjustmentAllowStrings($arg);
                        $returnValue += ($arg - $aMean) ** 2;
                        ++$aCount;
                    }
                }
            }

            if ($aCount > 0) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    /**
     * STDEVP.
     *
     * Calculates standard deviation based on the entire population
     *
     * Excel Function:
     *        STDEVP(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVP(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $aMean = Averages::AVERAGE($aArgs);

        if (!is_string($aMean)) {
            $returnValue = 0.0;
            $aCount = 0;

            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) || (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $returnValue += ($arg - $aMean) ** 2;
                    ++$aCount;
                }
            }

            if ($aCount > 0) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    /**
     * STDEVPA.
     *
     * Calculates standard deviation based on the entire population, including numbers, text, and logical values
     *
     * Excel Function:
     *        STDEVPA(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function STDEVPA(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $aMean = Averages::AVERAGEA($aArgs);

        if (!is_string($aMean)) {
            $returnValue = 0.0;
            $aCount = 0;

            foreach ($aArgs as $k => $arg) {
                if ((is_bool($arg)) && (!Functions::isMatrixValue($k))) {
                } else {
                    // Is it a numeric value?
                    if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) && ($arg != '')))) {
                        $arg = self::datatypeAdjustmentAllowStrings($arg);
                        $returnValue += ($arg - $aMean) ** 2;
                        ++$aCount;
                    }
                }
            }

            if ($aCount > 0) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }
}
