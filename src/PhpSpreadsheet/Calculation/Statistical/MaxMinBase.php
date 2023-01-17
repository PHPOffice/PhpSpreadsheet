<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

abstract class MaxMinBase
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function datatypeAdjustmentAllowStrings($value)
    {
        if (is_bool($value)) {
            return (int) $value;
        } elseif (is_string($value)) {
            return 0;
        }

        return $value;
    }
}
