<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Helpers
{
    public static function convertBooleanValue(bool $value): string
    {
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
            return $value ? '1' : '0';
        }

        return ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
    }

    /**
     * @param mixed $value String value from which to extract characters
     */
    public static function extractString($value, bool $throwIfError = false): string
    {
        if (is_bool($value)) {
            return self::convertBooleanValue($value);
        }
        if ($throwIfError && is_string($value) && ErrorValue::isError($value)) {
            throw new CalcExp($value);
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     */
    public static function extractInt($value, int $minValue, int $gnumericNull = 0, bool $ooBoolOk = false): int
    {
        if ($value === null) {
            // usually 0, but sometimes 1 for Gnumeric
            $value = (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_GNUMERIC) ? $gnumericNull : 0;
        }
        if (is_bool($value) && ($ooBoolOk || Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE)) {
            $value = (int) $value;
        }
        if (!is_numeric($value)) {
            throw new CalcExp(ExcelError::VALUE());
        }
        $value = (int) $value;
        if ($value < $minValue) {
            throw new CalcExp(ExcelError::VALUE());
        }

        return (int) $value;
    }

    /**
     * @param mixed $value
     */
    public static function extractFloat($value): float
    {
        if ($value === null) {
            $value = 0.0;
        }
        if (is_bool($value)) {
            $value = (float) $value;
        }
        if (!is_numeric($value)) {
            throw new CalcExp(ExcelError::VALUE());
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     */
    public static function validateInt($value): int
    {
        if ($value === null) {
            $value = 0;
        } elseif (is_bool($value)) {
            $value = (int) $value;
        }

        return (int) $value;
    }
}
