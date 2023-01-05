<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class Kasko
{
    /**
     * Replacing native function for php 8.0
     */
    public static function ROUND($num, $precision)
    {
        $num       = Functions::flattenSingleValue($num);
        $precision = Functions::flattenSingleValue($precision);

        return round((float) $num, $precision);
    }

    public static function INDEX($arrayValues, $rowNum = 0, $columnNum = 0)
    {
        $rowNum = Functions::flattenSingleValue($rowNum);
        $columnNum = Functions::flattenSingleValue($columnNum);

        // BC with our broken kasko insurer templates
        // This is obvious bug that was fixed: https://github.com/PHPOffice/PhpSpreadsheet/issues/2066
        if (Functions::NA() === $rowNum) {
            return $arrayValues;
        }

        if (($rowNum < 0) || ($columnNum < 0)) {
            return Functions::VALUE();
        }

        if (!is_array($arrayValues) || ($rowNum > count($arrayValues))) {
            return Functions::REF();
        }

        $rowKeys = array_keys($arrayValues);
        $columnKeys = @array_keys($arrayValues[$rowKeys[0]]);

        if ($columnNum > count($columnKeys)) {
            return Functions::VALUE();
        } elseif ($columnNum == 0) {
            if ($rowNum == 0) {
                return $arrayValues;
            }
            $rowNum = $rowKeys[--$rowNum];
            $returnArray = [];
            foreach ($arrayValues as $arrayColumn) {
                if (is_array($arrayColumn)) {
                    if (isset($arrayColumn[$rowNum])) {
                        $returnArray[] = $arrayColumn[$rowNum];
                    } else {
                        return [$rowNum => $arrayValues[$rowNum]];
                    }
                } else {
                    return $arrayValues[$rowNum];
                }
            }

            return $returnArray;
        }
        $columnNum = $columnKeys[--$columnNum];
        if ($rowNum > count($rowKeys)) {
            return Functions::VALUE();
        } elseif ($rowNum == 0) {
            return $arrayValues[$columnNum];
        }
        $rowNum = $rowKeys[--$rowNum];

        return $arrayValues[$rowNum][$columnNum];
    }
}
