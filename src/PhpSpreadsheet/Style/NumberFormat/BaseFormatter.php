<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

abstract class BaseFormatter
{
    protected static function stripQuotes(string $format): string
    {
        // Some non-number strings are quoted, so we'll get rid of the quotes, likewise any positional * symbols
        return str_replace(['"', '*'], '', $format);
    }

    protected static function adjustSeparators(string $value): string
    {
        $thousandsSeparator = StringHelper::getThousandsSeparator();
        $decimalSeparator = StringHelper::getDecimalSeparator();
        if ($thousandsSeparator !== ',' || $decimalSeparator !== '.') {
            $value = str_replace(['.', ',', "\u{fffd}"], ["\u{fffd}", $thousandsSeparator, $decimalSeparator], $value);
        }

        return $value;
    }
}
