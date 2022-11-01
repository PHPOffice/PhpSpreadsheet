<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NumberFormatter
{
    private const NUMBER_REGEX = '/(0+)(\\.?)(0*)/';

    private static function mergeComplexNumberFormatMasks(array $numbers, array $masks): array
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

    /**
     * @param mixed $number
     */
    private static function processComplexNumberFormatMask($number, string $mask): string
    {
        /** @var string */
        $result = $number;
        $maskingBlockCount = preg_match_all('/0+/', $mask, $maskingBlocks, PREG_OFFSET_CAPTURE);

        if ($maskingBlockCount > 1) {
            $maskingBlocks = array_reverse($maskingBlocks[0]);

            $offset = 0;
            foreach ($maskingBlocks as $block) {
                $size = strlen($block[0]);
                $divisor = 10 ** $size;
                $offset = $block[1];

                /** @var float */
                $numberFloat = $number;
                $blockValue = sprintf("%0{$size}d", fmod($numberFloat, $divisor));
                $number = floor($numberFloat / $divisor);
                $mask = substr_replace($mask, $blockValue, $offset, $size);
            }
            /** @var string */
            $numberString = $number;
            if ($number > 0) {
                $mask = substr_replace($mask, $numberString, $offset, 0);
            }
            $result = $mask;
        }

        return self::makeString($result);
    }

    /**
     * @param mixed $number
     */
    private static function complexNumberFormatMask($number, string $mask, bool $splitOnPoint = true): string
    {
        /** @var float */
        $numberFloat = $number;
        if ($splitOnPoint) {
            $masks = explode('.', $mask);
            if (count($masks) <= 2) {
                $decmask = $masks[1] ?? '';
                $decpos = substr_count($decmask, '0');
                $numberFloat = round($numberFloat, $decpos);
            }
        }
        $sign = ($numberFloat < 0.0) ? '-' : '';
        $number = self::f2s(abs($numberFloat));

        if ($splitOnPoint && strpos($mask, '.') !== false && strpos($number, '.') !== false) {
            $numbers = explode('.', $number);
            $masks = explode('.', $mask);
            if (count($masks) > 2) {
                $masks = self::mergeComplexNumberFormatMasks($numbers, $masks);
            }
            $integerPart = self::complexNumberFormatMask($numbers[0], $masks[0], false);
            $numlen = strlen($numbers[1]);
            $msklen = strlen($masks[1]);
            if ($numlen < $msklen) {
                $numbers[1] .= str_repeat('0', $msklen - $numlen);
            }
            $decimalPart = strrev(self::complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1]), false));
            $decimalPart = substr($decimalPart, 0, $msklen);

            return "{$sign}{$integerPart}.{$decimalPart}";
        }

        if (strlen($number) < strlen($mask)) {
            $number = str_repeat('0', strlen($mask) - strlen($number)) . $number;
        }
        $result = self::processComplexNumberFormatMask($number, $mask);

        return "{$sign}{$result}";
    }

    public static function f2s(float $f): string
    {
        return self::floatStringConvertScientific((string) $f);
    }

    public static function floatStringConvertScientific(string $s): string
    {
        // convert only normalized form of scientific notation:
        //  optional sign, single digit 1-9,
        //    decimal point and digits (allowed to be omitted),
        //    E (e permitted), optional sign, one or more digits
        if (preg_match('/^([+-])?([1-9])([.]([0-9]+))?[eE]([+-]?[0-9]+)$/', $s, $matches) === 1) {
            $exponent = (int) $matches[5];
            $sign = ($matches[1] === '-') ? '-' : '';
            if ($exponent >= 0) {
                $exponentPlus1 = $exponent + 1;
                $out = $matches[2] . $matches[4];
                $len = strlen($out);
                if ($len < $exponentPlus1) {
                    $out .= str_repeat('0', $exponentPlus1 - $len);
                }
                $out = substr($out, 0, $exponentPlus1) . ((strlen($out) === $exponentPlus1) ? '' : ('.' . substr($out, $exponentPlus1)));
                $s = "$sign$out";
            } else {
                $s = $sign . '0.' . str_repeat('0', -$exponent - 1) . $matches[2] . $matches[4];
            }
        }

        return $s;
    }

    /**
     * @param mixed $value
     */
    private static function formatStraightNumericValue($value, string $format, array $matches, bool $useThousands): string
    {
        /** @var float */
        $valueFloat = $value;
        $left = $matches[1];
        $dec = $matches[2];
        $right = $matches[3];

        // minimun width of formatted number (including dot)
        $minWidth = strlen($left) + strlen($dec) + strlen($right);
        if ($useThousands) {
            $value = number_format(
                $valueFloat,
                strlen($right),
                StringHelper::getDecimalSeparator(),
                StringHelper::getThousandsSeparator()
            );

            return self::pregReplace(self::NUMBER_REGEX, $value, $format);
        }

        if (preg_match('/[0#]E[+-]0/i', $format)) {
            //    Scientific format
            return sprintf('%5.2E', $valueFloat);
        } elseif (preg_match('/0([^\d\.]+)0/', $format) || substr_count($format, '.') > 1) {
            if ($valueFloat == floor($valueFloat) && substr_count($format, '.') === 1) {
                $value *= 10 ** strlen(explode('.', $format)[1]);
            }

            $result = self::complexNumberFormatMask($value, $format);
            if (strpos($result, 'E') !== false) {
                // This is a hack and doesn't match Excel.
                // It will, at least, be an accurate representation,
                //  even if formatted incorrectly.
                // This is needed for absolute values >=1E18.
                $result = self::f2s($valueFloat);
            }

            return $result;
        }

        $sprintf_pattern = "%0$minWidth." . strlen($right) . 'f';
        /** @var float */
        $valueFloat = $value;
        $value = sprintf($sprintf_pattern, round($valueFloat, strlen($right)));

        return self::pregReplace(self::NUMBER_REGEX, $value, $format);
    }

    /**
     * @param mixed $value
     */
    public static function format($value, string $format): string
    {
        // The "_" in this string has already been stripped out,
        // so this test is never true. Furthermore, testing
        // on Excel shows this format uses Euro symbol, not "EUR".
        //if ($format === NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE) {
        //    return 'EUR ' . sprintf('%1.2f', $value);
        //}

        // Some non-number strings are quoted, so we'll get rid of the quotes, likewise any positional * symbols
        $format = self::makeString(str_replace(['"', '*'], '', $format));

        // Find out if we need thousands separator
        // This is indicated by a comma enclosed by a digit placeholder:
        //        #,#   or   0,0
        $useThousands = (bool) preg_match('/(#,#|0,0)/', $format);
        if ($useThousands) {
            $format = self::pregReplace('/0,0/', '00', $format);
            $format = self::pregReplace('/#,#/', '##', $format);
        }

        // Scale thousands, millions,...
        // This is indicated by a number of commas after a digit placeholder:
        //        #,   or    0.0,,
        $scale = 1; // same as no scale
        $matches = [];
        if (preg_match('/(#|0)(,+)/', $format, $matches)) {
            $scale = 1000 ** strlen($matches[2]);

            // strip the commas
            $format = self::pregReplace('/0,+/', '0', $format);
            $format = self::pregReplace('/#,+/', '#', $format);
        }
        if (preg_match('/#?.*\?\/\?/', $format, $m)) {
            $value = FractionFormatter::format($value, $format);
        } else {
            // Handle the number itself

            // scale number
            $value = $value / $scale;
            // Strip #
            $format = self::pregReplace('/\\#/', '0', $format);
            // Remove locale code [$-###]
            $format = self::pregReplace('/\[\$\-.*\]/', '', $format);

            $n = '/\\[[^\\]]+\\]/';
            $m = self::pregReplace($n, '', $format);
            if (preg_match(self::NUMBER_REGEX, $m, $matches)) {
                // There are placeholders for digits, so inject digits from the value into the mask
                $value = self::formatStraightNumericValue($value, $format, $matches, $useThousands);
            } elseif ($format !== NumberFormat::FORMAT_GENERAL) {
                // Yes, I know that this is basically just a hack;
                //      if there's no placeholders for digits, just return the format mask "as is"
                $value = self::makeString(str_replace('?', '', $format));
            }
        }

        if (preg_match('/\[\$(.*)\]/u', $format, $m)) {
            //  Currency or Accounting
            $currencyCode = $m[1];
            [$currencyCode] = explode('-', $currencyCode);
            if ($currencyCode == '') {
                $currencyCode = StringHelper::getCurrencyCode();
            }
            $value = self::pregReplace('/\[\$([^\]]*)\]/u', $currencyCode, (string) $value);
        }

        return (string) $value;
    }

    /**
     * @param array|string $value
     */
    private static function makeString($value): string
    {
        return is_array($value) ? '' : "$value";
    }

    private static function pregReplace(string $pattern, string $replacement, string $subject): string
    {
        return self::makeString(preg_replace($pattern, $replacement, $subject) ?? '');
    }
}
