<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BaseParserClass
{
    /**
     * @param mixed $value
     */
    protected static function boolean($value): bool
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
