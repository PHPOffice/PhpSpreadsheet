<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NumberFormatter
{
    private const NUMBER_REGEX = '/(0+)(\\.?)(0*)/';

    private static function mergeComplexNumberFormatMasks($numbers, $masks): array
    {
        $decimalCount = strlen($numbers[1]);
        $postDecimalMasks = [];

        do {
            $tempMask = array_pop($masks);
            if ($tempMask !== null) {
                $postDecimalMasks[] = $tempMask;
                $decimalCount -= strlen($tempMask);
            }
        } while ($tempMask !== null && $decimalCount > 0);

        return [
            implode('.', $masks),
            implode('.', array_reverse($postDecimalMasks)),
        ];
    }

    private static function processComplexNumberFormatMask($number, $mask): string
    {
        $result = $number;
        $maskingBlockCount = preg_match_all('/0+/', $mask, $maskingBlocks, PREG_OFFSET_CAPTURE);

        if ($maskingBlockCount > 1) {
            $maskingBlocks = array_reverse($maskingBlocks[0]);

            $offset = 0;
            foreach ($maskingBlocks as $block) {
                $size = strlen($block[0]);
                $divisor = 10 ** $size;
                $offset = $block[1];

                $blockValue = sprintf("%0{$size}d", fmod($number, $divisor));
                $number = floor($number / $divisor);
                $mask = substr_replace($mask, $blockValue, $offset, $size);
            }
            if ($number > 0) {
                $mask = substr_replace($mask, $number, $offset, 0);
            }
            $result = $mask;
        }

        return $result;
    }

    private static function complexNumberFormatMask($number, $mask, $splitOnPoint = true): string
    {
        $sign = ($number < 0.0) ? '-' : '';
        $number = abs($number);

        if ($splitOnPoint && strpos($mask, '.') !== false && strpos($number, '.') !== false) {
            $numbers = explode('.', $number);
            $masks = explode('.', $mask);
            if (count($masks) > 2) {
                $masks = self::mergeComplexNumberFormatMasks($numbers, $masks);
            }
            $integerPart = self::complexNumberFormatMask($numbers[0], $masks[0], false);
            $decimalPart = strrev(self::complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1]), false));

            return "{$sign}{$integerPart}.{$decimalPart}";
        }

        $result = self::processComplexNumberFormatMask($number, $mask);

        return "{$sign}{$result}";
    }

    private static function formatStraightNumericValue($value, $format, array $matches, $useThousands): string
    {
        $left = $matches[1];
        $dec = $matches[2];
        $right = $matches[3];

        // minimun width of formatted number (including dot)
        $minWidth = strlen($left) + strlen($dec) + strlen($right);
        if ($useThousands) {
            $value = number_format(
                $value,
                strlen($right),
                StringHelper::getDecimalSeparator(),
                StringHelper::getThousandsSeparator()
            );

            return preg_replace(self::NUMBER_REGEX, $value, $format);
        }

        if (preg_match('/[0#]E[+-]0/i', $format)) {
            //    Scientific format
            return sprintf('%5.2E', $value);
        } elseif (preg_match('/0([^\d\.]+)0/', $format) || substr_count($format, '.') > 1) {
            if ($value == (int) $value && substr_count($format, '.') === 1) {
                $value *= 10 ** strlen(explode('.', $format)[1]);
            }

            return self::complexNumberFormatMask($value, $format);
        }

        $sprintf_pattern = "%0$minWidth." . strlen($right) . 'f';
        $value = sprintf($sprintf_pattern, $value);

        return preg_replace(self::NUMBER_REGEX, $value, $format);
    }

    public static function format($value, $format): string
    {
        // The "_" in this string has already been stripped out,
        // so this test is never true. Furthermore, testing
        // on Excel shows this format uses Euro symbol, not "EUR".
        //if ($format === NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE) {
        //    return 'EUR ' . sprintf('%1.2f', $value);
        //}

        // Some non-number strings are quoted, so we'll get rid of the quotes, likewise any positional * symbols
        $format = str_replace(['"', '*'], '', $format);

        // Find out if we need thousands separator
        // This is indicated by a comma enclosed by a digit placeholder:
        //        #,#   or   0,0
        $useThousands = preg_match('/(#,#|0,0)/', $format);
        if ($useThousands) {
            $format = preg_replace('/0,0/', '00', $format);
            $format = preg_replace('/#,#/', '##', $format);
        }

        // Scale thousands, millions,...
        // This is indicated by a number of commas after a digit placeholder:
        //        #,   or    0.0,,
        $scale = 1; // same as no scale
        $matches = [];
        if (preg_match('/(#|0)(,+)/', $format, $matches)) {
            $scale = 1000 ** strlen($matches[2]);

            // strip the commas
            $format = preg_replace('/0,+/', '0', $format);
            $format = preg_replace('/#,+/', '#', $format);
        }
        if (preg_match('/#?.*\?\/\?/', $format, $m)) {
            if ($value != (int) $value) {
                $value = FractionFormatter::format($value, $format);
            }
        } else {
            // Handle the number itself

            // scale number
            $value = $value / $scale;
            // Strip #
            $format = preg_replace('/\\#/', '0', $format);
            // Remove locale code [$-###]
            $format = preg_replace('/\[\$\-.*\]/', '', $format);

            $n = '/\\[[^\\]]+\\]/';
            $m = preg_replace($n, '', $format);
            if (preg_match(self::NUMBER_REGEX, $m, $matches)) {
                // There are placeholders for digits, so inject digits from the value into the mask
                $value = self::formatStraightNumericValue($value, $format, $matches, $useThousands);
            } elseif ($format !== NumberFormat::FORMAT_GENERAL) {
                // Yes, I know that this is basically just a hack;
                //      if there's no placeholders for digits, just return the format mask "as is"
                $value = str_replace('?', '', $format ?? '');
            }
        }

        if (preg_match('/\[\$(.*)\]/u', $format, $m)) {
            //  Currency or Accounting
            $currencyCode = $m[1];
            [$currencyCode] = explode('-', $currencyCode);
            if ($currencyCode == '') {
                $currencyCode = StringHelper::getCurrencyCode();
            }
            $value = preg_replace('/\[\$([^\]]*)\]/u', $currencyCode, $value);
        }

        return $value;
    }
}
