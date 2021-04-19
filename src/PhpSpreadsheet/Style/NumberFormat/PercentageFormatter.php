<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PercentageFormatter extends BaseFormatter
{
    public static function format($value, string $format): string
    {
        if ($format === NumberFormat::FORMAT_PERCENTAGE) {
            return round((100 * $value), 0) . '%';
        }

        $value *= 100;
        $format = self::stripQuotes($format);

        [, $vDecimals] = explode('.', ((string) $value) . '.');
        $vDecimalCount = strlen(rtrim($vDecimals, '0'));

        $format = str_replace('%', '%%', $format);
        $wholePartSize = strlen((string) floor($value));
        $decimalPartSize = $placeHolders = 0;
        // Number of decimals
        if (preg_match('/\.([?0]+)/u', $format, $matches)) {
            $decimalPartSize = strlen($matches[1]);
            $vMinDecimalCount = strlen(rtrim($matches[1], '?'));
            $decimalPartSize = min(max($vMinDecimalCount, $vDecimalCount), $decimalPartSize);
            $placeHolders = str_repeat(' ', strlen($matches[1]) - $decimalPartSize);
        }
        // Number of digits to display before the decimal
        if (preg_match('/([#0,]+)\./u', $format, $matches)) {
            $wholePartSize = max($wholePartSize, strlen($matches[1]));
        }

        $wholePartSize += $decimalPartSize;
        $replacement = "{$wholePartSize}.{$decimalPartSize}";
        $mask = preg_replace('/[#0,]+\.?[?#0,]*/ui', "%{$replacement}f{$placeHolders}", $format);

        return sprintf($mask, $value);
    }
}
