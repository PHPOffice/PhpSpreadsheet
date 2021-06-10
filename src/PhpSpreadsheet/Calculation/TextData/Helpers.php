<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
    public static function extractString($value): string
    {
        $value = Functions::flattenSingleValue($value);
        if (is_bool($value)) {
            return self::convertBooleanValue($value);
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     */
    public static function extractInt($value, int $minValue, int $gnumericNull = 0, bool $ooBoolOk = false): int
    {
        $value = Functions::flattenSingleValue($value);
        if ($value === null) {
            // usually 0, but sometimes 1 for Gnumeric
            $value = (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_GNUMERIC) ? $gnumericNull : 0;
        }
        if (is_bool($value) && ($ooBoolOk || Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE)) {
            $value = (int) $value;
        }
        if (!is_numeric($value)) {
            throw new CalcExp(Functions::VALUE());
        }
        $value = (int) $value;
        if ($value < $minValue) {
            throw new CalcExp(Functions::VALUE());
        }

        return (int) $value;
    }

    /**
     * @param mixed $value
     */
    public static function extractFloat($value): float
    {
        $value = Functions::flattenSingleValue($value);
        if ($value === null) {
            $value = 0.0;
        }
        if (is_bool($value)) {
            $value = (float) $value;
        }
        if (!is_numeric($value)) {
            throw new CalcExp(Functions::VALUE());
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     */
    public static function validateInt($value): int
    {
        $value = Functions::flattenSingleValue($value);
        if ($value === null) {
            $value = 0;
        } elseif (is_bool($value)) {
            $value = (int) $value;
        }

        return (int) $value;
    }
}
