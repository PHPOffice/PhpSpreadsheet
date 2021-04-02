<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

trait BaseValidations
{
    protected static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
    }

    protected static function validateInt($value): int
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) floor($value);
    }
}
