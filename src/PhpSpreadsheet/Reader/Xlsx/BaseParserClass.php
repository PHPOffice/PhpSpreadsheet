<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class BaseParserClass
{
    protected static function boolean(mixed $value): bool
    {
        if (is_object($value)) {
            $value = (string) $value; // @phpstan-ignore-line
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === 'true' || $value === 'TRUE';
    }
}
