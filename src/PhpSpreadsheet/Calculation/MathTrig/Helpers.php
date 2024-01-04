<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Helpers
{
    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @return float|string quotient or DIV0 if denominator is too small
     */
    public static function verySmallDenominator(float $numerator, float $denominator): string|float
    {
        return (abs($denominator) < 1.0E-12) ? ExcelError::DIV0() : ($numerator / $denominator);
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     */
    public static function validateNumericNullBool(mixed $number): int|float
    {
        $number = Functions::flattenSingleValue($number);
        if ($number === null) {
            return 0;
        }
        if (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            return 0 + $number;
        }

        throw new Exception(ExcelError::throwError($number));
    }

    /**
     * Validate numeric, but allow substitute for null.
     */
    public static function validateNumericNullSubstitution(mixed $number, null|float|int $substitute): float|int
    {
        $number = Functions::flattenSingleValue($number);
        if ($number === null && $substitute !== null) {
            return $substitute;
        }
        if (is_numeric($number)) {
            return 0 + $number;
        }

        throw new Exception(ExcelError::throwError($number));
    }

    /**
     * Confirm number >= 0.
     */
    public static function validateNotNegative(float|int $number, ?string $except = null): void
    {
        if ($number >= 0) {
            return;
        }

        throw new Exception($except ?? ExcelError::NAN());
    }

    /**
     * Confirm number > 0.
     */
    public static function validatePositive(float|int $number, ?string $except = null): void
    {
        if ($number > 0) {
            return;
        }

        throw new Exception($except ?? ExcelError::NAN());
    }

    /**
     * Confirm number != 0.
     */
    public static function validateNotZero(float|int $number): void
    {
        if ($number) {
            return;
        }

        throw new Exception(ExcelError::DIV0());
    }

    public static function returnSign(float $number): int
    {
        return $number ? (($number > 0) ? 1 : -1) : 0;
    }

    public static function getEven(float $number): float
    {
        $significance = 2 * self::returnSign($number);

        return $significance ? (ceil($number / $significance) * $significance) : 0;
    }

    /**
     * Return NAN or value depending on argument.
     */
    public static function numberOrNan(float $result): float|string
    {
        return is_nan($result) ? ExcelError::NAN() : $result;
    }
}
