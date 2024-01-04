<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

class IntOrFloat
{
    /**
     * Help some functions with large results operate correctly on 32-bit,
     * by returning result as int when possible, float otherwise.
     */
    public static function evaluate(float|int $value): float|int
    {
        $iValue = (int) $value;

        return ($value == $iValue) ? $iValue : $value;
    }
}
