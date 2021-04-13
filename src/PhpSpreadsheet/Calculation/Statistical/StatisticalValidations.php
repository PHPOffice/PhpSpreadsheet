<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class StatisticalValidations
{
    public static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
    }

    public static function validateInt($value): int
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) floor($value);
    }

    public static function validateBool($value): bool
    {
        if (!is_bool($value) && !is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (bool) $value;
    }
}
