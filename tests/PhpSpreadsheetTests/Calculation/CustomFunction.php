<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class CustomFunction
{
    public static function fourthPower(mixed $number): float|int|string
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (CalcException $e) {
            return $e->getMessage();
        }

        return $number ** 4;
    }

    /**
     * ASC.
     * Converts full-width (double-byte) characters to half-width (single-byte) characters.
     * There are many difficulties with implementing this into PhpSpreadsheet.
     */
    public static function ASC(mixed $stringValue): string
    {
        /*if (is_array($stringValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $stringValue);
        }*/
        $stringValue = StringHelper::convertToString($stringValue, convertBool: true);
        if (function_exists('mb_convert_kana')) {
            return mb_convert_kana($stringValue, 'a', 'UTF-8');
        }
        // Fallback if mb_convert_kana is not available.
        // PhpSpreadsheet heavily relies on mbstring, so this is more of a theoretical fallback.
        // A comprehensive manual conversion is extensive.

        return $stringValue;
    }
}
