<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

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

    protected static function validateBool($value): bool
    {
        if (!is_bool($value) && !is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (bool) $value;
    }

    protected static function validateProbability($probability)
    {
        $probability = self::validateFloat($probability);

        if ($probability < 0.0 || $probability > 1.0) {
            throw new Exception(Functions::NAN());
        }

        return $probability;
    }
}
