<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

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
     */
    public static function sumIgnoringStrings(mixed ...$args): float|int|string
    {
        $returnValue = 0;

        // Loop through the arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                $returnValue += $arg;
            } elseif (ErrorValue::isError($arg)) {
                /** @var string $arg */
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
     * @return array<mixed>|float|int|string
     */
    public static function sumErroringStrings(mixed ...$args): float|int|string|array
    {
        $returnValue = 0;
        // Loop through the arguments
        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                $returnValue += $arg;
            } elseif (is_bool($arg)) {
                $returnValue += (int) $arg;
            } elseif (ErrorValue::isError($arg, true)) {
                /** @var string $arg */
                return $arg;
            } elseif ($arg !== null && !Functions::isCellValue($k)) {
                // ignore non-numerics from cell, but fail as literals (except null)
                return ExcelError::VALUE();
            }
        }

        return $returnValue;
    }

    /**
     * SUMPRODUCT.
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|int|string The result, or a string containing an error
     */
    public static function product(mixed ...$args): string|int|float
    {
        $arrayList = $args;

        $wrkArray = Functions::flattenArray(array_shift($arrayList));
        $wrkCellCount = count($wrkArray);

        for ($i = 0; $i < $wrkCellCount; ++$i) {
            if ((!is_numeric($wrkArray[$i])) || (is_string($wrkArray[$i]))) {
                $wrkArray[$i] = 0;
            }
        }

        foreach ($arrayList as $matrixData) {
            $array2 = Functions::flattenArray($matrixData);
            $count = count($array2);
            if ($wrkCellCount != $count) {
                return ExcelError::VALUE();
            }

            foreach ($array2 as $i => $val) {
                if ((!is_numeric($val)) || (is_string($val))) {
                    $val = 0;
                }
                /** @var array<float|int> $wrkArray */
                $wrkArray[$i] *= $val;
            }
        }

        return array_sum($wrkArray);
    }
}
