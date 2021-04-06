<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SumProduct
{
    /**
     * SUMPRODUCT.
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function funcSumProduct(...$args)
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
                return Functions::VALUE();
            }

            foreach ($array2 as $i => $val) {
                if ((!is_numeric($val)) || (is_string($val))) {
                    $val = 0;
                }
                $wrkArray[$i] *= $val;
            }
        }

        return array_sum($wrkArray);
    }
}
