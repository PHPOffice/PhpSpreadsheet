<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class FractionFormatter extends BaseFormatter
{
    /** @param null|bool|float|int|string $value  value to be formatted */
    public static function format(mixed $value, string $format): string
    {
        // A 0 placeholder for the last digit of the numerator shows a numerator of 0
        $showZeroNumerator = str_contains($format, '0/');
        // It's a dirty hack; but replace # and 0 digit placeholders with ?
        $format = (string) preg_replace('/[#0]+\//', '?/', $format);
        $format = (string) preg_replace('/\/[#0]+/', '/?', $format);
        $format = self::stripQuotes($format);
        $value = (float) $value;
        $absValue = abs($value);

        $sign = ($value < 0.0) ? '-' : '';

        $integerPart = floor($absValue);

        preg_match('/(#?.*\?)\/(\?+|\d+)/', $format, $matches);
        $formatIntegerPart = $matches[1] ?? '0';
        $fixedDivisor = (isset($matches[2]) && is_numeric($matches[2])) ? (int) $matches[2] : null;
        $isVulgar = !str_contains($formatIntegerPart, '0')
            && !str_contains($formatIntegerPart, '#')
            && !str_starts_with($formatIntegerPart, '? ?');

        $decimalPart = self::getDecimal((string) $absValue);
        if ($decimalPart === '0') {
            // An integer is written as a fraction when the format has no integer part,
            // or when the numerator shows 0; otherwise the fraction is left out
            if (!$isVulgar && !$showZeroNumerator) {
                return "{$sign}{$integerPart}";
            }
            $adjustedDecimalPart = 0;
            $adjustedDecimalDivisor = $fixedDivisor ?? 1;
        } else {
            $decimalLength = strlen($decimalPart);
            $decimalDivisor = 10 ** $decimalLength;

            if ($fixedDivisor !== null) {
                $fractionDivisor = 100 / $fixedDivisor;
            } else {
                /** @var float $fractionDivisor */
                $fractionDivisor = MathTrig\Gcd::evaluate((int) $decimalPart, $decimalDivisor);
            }

            $adjustedDecimalPart = (int) round((int) $decimalPart / $fractionDivisor, 0);
            $adjustedDecimalDivisor = $decimalDivisor / $fractionDivisor;
        }

        if ((str_contains($formatIntegerPart, '0'))) {
            return "{$sign}{$integerPart} {$adjustedDecimalPart}/{$adjustedDecimalDivisor}";
        } elseif ((str_contains($formatIntegerPart, '#'))) {
            if ($integerPart == 0) {
                return "{$sign}{$adjustedDecimalPart}/{$adjustedDecimalDivisor}";
            }

            return "{$sign}{$integerPart} {$adjustedDecimalPart}/{$adjustedDecimalDivisor}";
        } elseif ((str_starts_with($formatIntegerPart, '? ?'))) {
            if ($integerPart == 0) {
                $integerPart = '';
            }

            return "{$sign}{$integerPart} {$adjustedDecimalPart}/{$adjustedDecimalDivisor}";
        }

        $adjustedDecimalPart += $integerPart * $adjustedDecimalDivisor;

        return "{$sign}{$adjustedDecimalPart}/{$adjustedDecimalDivisor}";
    }

    private static function getDecimal(string $value): string
    {
        $decimalPart = '0';
        if (preg_match('/^\d*[.](\d*[1-9])0*$/', $value, $matches) === 1) {
            $decimalPart = $matches[1];
        }

        return $decimalPart;
    }
}
