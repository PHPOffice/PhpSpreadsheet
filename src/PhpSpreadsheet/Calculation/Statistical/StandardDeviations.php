<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class StandardDeviations
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
    public static function stdev(...$args)
    {
        $result = Variances::variance(...$args);
        if (!is_numeric($result)) {
            return $result;
        }

        return sqrt((float) $result);
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
    public static function stdevA(...$args)
    {
        $result = Variances::varianceA(...$args);
        if (!is_numeric($result)) {
            return $result;
        }

        return sqrt((float) $result);
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
    public static function stdevP(...$args)
    {
        $result = Variances::varianceP(...$args);
        if (!is_numeric($result)) {
            return $result;
        }

        return sqrt((float) $result);
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
    public static function stdevPA(...$args)
    {
        $result = Variances::variancePA(...$args);
        if (!is_numeric($result)) {
            return $result;
        }

        return sqrt((float) $result);
    }
}
