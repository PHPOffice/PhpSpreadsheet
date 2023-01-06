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
        // BC for our broken kasko insurer templates
        // This is obvious bug that was fixed: https://github.com/PHPOffice/PhpSpreadsheet/issues/2066
        if (Functions::NA() === Functions::flattenSingleValue($rowNum)) {
            return $arrayValues;
        }

        return LookupRef::INDEX($arrayValues, $rowNum, $columnNum);
    }
}
