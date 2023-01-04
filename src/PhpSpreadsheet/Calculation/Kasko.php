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
}
