<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Sum
{
    /**
     * SUM, ignoring non-numeric non-error strings. This is eventually used by SUMIF.
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function funcSum(...$args)
    {
        $returnValue = 0;

        // Loop through the arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                $returnValue += $arg;
            } elseif (Functions::isError($arg)) {
                return $arg;
            }
        }

        return $returnValue;
    }

    /**
     * SUM, returning error for non-numeric strings. This is used by Excel SUM function.
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function funcSumNoStrings(...$args)
    {
        $returnValue = 0;

        // Loop through the arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                $returnValue += $arg;
            } elseif (Functions::isError($arg)) {
                return $arg;
            } else {
                return Functions::VALUE();
            }
        }

        return $returnValue;
    }
}
