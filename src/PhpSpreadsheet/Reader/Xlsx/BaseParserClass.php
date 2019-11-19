<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BaseParserClass
{
    protected static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === strtolower('true');
    }
}
