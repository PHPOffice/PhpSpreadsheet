<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Formatter
{
    /**
     * Search/replace values to convert Excel date/time format masks to PHP format masks.
     *
     * @var array
     */
    private static $dateFormatReplacements = [
        // first remove escapes related to non-format characters
        '\\' => '',
        //    12-hour suffix
        'am/pm' => 'A',
        //    4-digit year
        'e' => 'Y',
        'yyyy' => 'Y',
        //    2-digit year
        'yy' => 'y',
        //    first letter of month - no php equivalent
        'mmmmm' => 'M',
        //    full month name
        'mmmm' => 'F',
        //    short month name
        'mmm' => 'M',
        //    mm is minutes if time, but can also be month w/leading zero
        //    so we try to identify times be the inclusion of a : separator in the mask
        //    It isn't perfect, but the best way I know how
        ':mm' => ':i',
        'mm:' => 'i:',
        //    month leading zero
        'mm' => 'm',
        //    month no leading zero
        'm' => 'n',
        //    full day of week name
        'dddd' => 'l',
        //    short day of week name
        'ddd' => 'D',
        //    days leading zero
        'dd' => 'd',
        //    days no leading zero
        'd' => 'j',
        //    seconds
        'ss' => 's',
        //    fractional seconds - no php equivalent
        '.s' => '',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock).
     *
     * @var array
     */
    private static $dateFormatReplacements24 = [
        'hh' => 'H',
        'h' => 'G',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock).
     *
     * @var array
     */
    private static $dateFormatReplacements12 = [
        'hh' => 'h',
        'h' => 'g',
    ];

    private static function setLowercaseCallback($matches)
    {
        return mb_strtolower($matches[0]);
    }

    private static function escapeQuotesCallback($matches)
    {
        return '\\' . implode('\\', str_split($matches[1]));
    }

    private static function formatAsDate(&$value, &$format): void
    {
        // strip off first part containing e.g. [$-F800] or [$USD-409]
        // general syntax: [$<Currency string>-<language info>]
        // language info is in hexadecimal
        // strip off chinese part like [DBNum1][$-804]
        $format = preg_replace('/^(\[DBNum\d\])*(\[\$[^\]]*\])/i', '', $format);

        // OpenOffice.org uses upper-case number formats, e.g. 'YYYY', convert to lower-case;
        //    but we don't want to change any quoted strings
        $format = preg_replace_callback('/(?:^|")([^"]*)(?:$|")/', ['self', 'setLowercaseCallback'], $format);

        // Only process the non-quoted blocks for date format characters
        $blocks = explode('"', $format);
        foreach ($blocks as $key => &$block) {
            if ($key % 2 == 0) {
                $block = strtr($block, self::$dateFormatReplacements);
                if (!strpos($block, 'A')) {
                    // 24-hour time format
                    // when [h]:mm format, the [h] should replace to the hours of the value * 24
                    if (false !== strpos($block, '[h]')) {
                        $hours = (int) ($value * 24);
                        $block = str_replace('[h]', $hours, $block);

                        continue;
                    }
                    $block = strtr($block, self::$dateFormatReplacements24);
                } else {
                    // 12-hour time format
                    $block = strtr($block, self::$dateFormatReplacements12);
                }
            }
        }
        $format = implode('"', $blocks);

        // escape any quoted characters so that DateTime format() will render them correctly
        $format = preg_replace_callback('/"(.*)"/U', ['self', 'escapeQuotesCallback'], $format);

        $dateObj = Date::excelToDateTimeObject($value);
        // If the colon preceding minute had been quoted, as happens in
        // Excel 2003 XML formats, m will not have been changed to i above.
        // Change it now.
        $format = \preg_replace('/\\\\:m/', ':i', $format);
        $value = $dateObj->format($format);
    }

    private static function formatAsPercentage(&$value, &$format): void
    {
        if ($format === NumberFormat::FORMAT_PERCENTAGE) {
            $value = round((100 * $value), 0) . '%';
        } else {
            if (preg_match('/\.[#0]+/', $format, $m)) {
                $s = substr($m[0], 0, 1) . (strlen($m[0]) - 1);
                $format = str_replace($m[0], $s, $format);
            }
            if (preg_match('/^[#0]+/', $format, $m)) {
                $format = str_replace($m[0], strlen($m[0]), $format);
            }
            $format = '%' . str_replace('%', 'f%%', $format);

            $value = sprintf($format, 100 * $value);
        }
    }

    private static function formatAsFraction(&$value, &$format): void
    {
        $sign = ($value < 0) ? '-' : '';

        $integerPart = floor(abs($value));
        $decimalPart = trim(fmod(abs($value), 1), '0.');
        $decimalLength = strlen($decimalPart);
        $decimalDivisor = 10 ** $decimalLength;

        $GCD = MathTrig::GCD($decimalPart, $decimalDivisor);

        $adjustedDecimalPart = $decimalPart / $GCD;
        $adjustedDecimalDivisor = $decimalDivisor / $GCD;

        if ((strpos($format, '0') !== false)) {
            $value = "$sign$integerPart $adjustedDecimalPart/$adjustedDecimalDivisor";
        } elseif ((strpos($format, '#') !== false)) {
            if ($integerPart == 0) {
                $value = "$sign$adjustedDecimalPart/$adjustedDecimalDivisor";
            } else {
                $value = "$sign$integerPart $adjustedDecimalPart/$adjustedDecimalDivisor";
            }
        } elseif ((substr($format, 0, 3) == '? ?')) {
            if ($integerPart == 0) {
                $integerPart = '';
            }
            $value = "$sign$integerPart $adjustedDecimalPart/$adjustedDecimalDivisor";
        } else {
            $adjustedDecimalPart += $integerPart * $adjustedDecimalDivisor;
            $value = "$sign$adjustedDecimalPart/$adjustedDecimalDivisor";
        }
    }

    private static function mergeComplexNumberFormatMasks($numbers, $masks)
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

    private static function processComplexNumberFormatMask($number, $mask)
    {
        $result = $number;
        $maskingBlockCount = preg_match_all('/0+/', $mask, $maskingBlocks, PREG_OFFSET_CAPTURE);

        if ($maskingBlockCount > 1) {
            $maskingBlocks = array_reverse($maskingBlocks[0]);

            foreach ($maskingBlocks as $block) {
                $divisor = 1 . $block[0];
                $size = strlen($block[0]);
                $offset = $block[1];

                $blockValue = sprintf(
                    '%0' . $size . 'd',
                    fmod($number, $divisor)
                );
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

    private static function complexNumberFormatMask($number, $mask, $splitOnPoint = true)
    {
        $sign = ($number < 0.0);
        $number = abs($number);

        if ($splitOnPoint && strpos($mask, '.') !== false && strpos($number, '.') !== false) {
            $numbers = explode('.', $number);
            $masks = explode('.', $mask);
            if (count($masks) > 2) {
                $masks = self::mergeComplexNumberFormatMasks($numbers, $masks);
            }
            $result1 = self::complexNumberFormatMask($numbers[0], $masks[0], false);
            $result2 = strrev(self::complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1]), false));

            return (($sign) ? '-' : '') . $result1 . '.' . $result2;
        }

        $result = self::processComplexNumberFormatMask($number, $mask);

        return (($sign) ? '-' : '') . $result;
    }

    private static function formatStraightNumericValue($value, $format, array $matches, $useThousands, $number_regex)
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
            $value = preg_replace($number_regex, $value, $format);
        } else {
            if (preg_match('/[0#]E[+-]0/i', $format)) {
                //    Scientific format
                $value = sprintf('%5.2E', $value);
            } elseif (preg_match('/0([^\d\.]+)0/', $format) || substr_count($format, '.') > 1) {
                if ($value == (int) $value && substr_count($format, '.') === 1) {
                    $value *= 10 ** strlen(explode('.', $format)[1]);
                }
                $value = self::complexNumberFormatMask($value, $format);
            } else {
                $sprintf_pattern = "%0$minWidth." . strlen($right) . 'f';
                $value = sprintf($sprintf_pattern, $value);
                $value = preg_replace($number_regex, $value, $format);
            }
        }

        return $value;
    }

    private static function formatAsNumber($value, $format)
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
                self::formatAsFraction($value, $format);
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
            $number_regex = '/(0+)(\\.?)(0*)/';
            if (preg_match($number_regex, $m, $matches)) {
                $value = self::formatStraightNumericValue($value, $format, $matches, $useThousands, $number_regex);
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

    private static function splitFormatCompare($value, $cond, $val, $dfcond, $dfval)
    {
        if (!$cond) {
            $cond = $dfcond;
            $val = $dfval;
        }
        switch ($cond) {
            case '>':
                return $value > $val;

            case '<':
                return $value < $val;

            case '<=':
                return $value <= $val;

            case '<>':
                return $value != $val;

            case '=':
                return $value == $val;
        }

        return $value >= $val;
    }

    private static function splitFormat($sections, $value)
    {
        // Extract the relevant section depending on whether number is positive, negative, or zero?
        // Text not supported yet.
        // Here is how the sections apply to various values in Excel:
        //   1 section:   [POSITIVE/NEGATIVE/ZERO/TEXT]
        //   2 sections:  [POSITIVE/ZERO/TEXT] [NEGATIVE]
        //   3 sections:  [POSITIVE/TEXT] [NEGATIVE] [ZERO]
        //   4 sections:  [POSITIVE] [NEGATIVE] [ZERO] [TEXT]
        $cnt = count($sections);
        $color_regex = '/\\[(' . implode('|', Color::NAMED_COLORS) . ')\\]/';
        $cond_regex = '/\\[(>|>=|<|<=|=|<>)([+-]?\\d+([.]\\d+)?)\\]/';
        $colors = ['', '', '', '', ''];
        $condops = ['', '', '', '', ''];
        $condvals = [0, 0, 0, 0, 0];
        for ($idx = 0; $idx < $cnt; ++$idx) {
            if (preg_match($color_regex, $sections[$idx], $matches)) {
                $colors[$idx] = $matches[0];
                $sections[$idx] = preg_replace($color_regex, '', $sections[$idx]);
            }
            if (preg_match($cond_regex, $sections[$idx], $matches)) {
                $condops[$idx] = $matches[1];
                $condvals[$idx] = $matches[2];
                $sections[$idx] = preg_replace($cond_regex, '', $sections[$idx]);
            }
        }
        $color = $colors[0];
        $format = $sections[0];
        $absval = $value;
        switch ($cnt) {
            case 2:
                $absval = abs($value);
                if (!self::splitFormatCompare($value, $condops[0], $condvals[0], '>=', 0)) {
                    $color = $colors[1];
                    $format = $sections[1];
                }

                break;
            case 3:
            case 4:
                $absval = abs($value);
                if (!self::splitFormatCompare($value, $condops[0], $condvals[0], '>', 0)) {
                    if (self::splitFormatCompare($value, $condops[1], $condvals[1], '<', 0)) {
                        $color = $colors[1];
                        $format = $sections[1];
                    } else {
                        $color = $colors[2];
                        $format = $sections[2];
                    }
                }

                break;
        }

        return [$color, $format, $absval];
    }

    /**
     * Convert a value in a pre-defined format to a PHP string.
     *
     * @param mixed $value Value to format
     * @param string $format Format code, see = NumberFormat::FORMAT_*
     * @param array $callBack Callback function for additional formatting of string
     *
     * @return string Formatted string
     */
    public static function toFormattedString($value, $format, $callBack = null)
    {
        // For now we do not treat strings although section 4 of a format code affects strings
        if (!is_numeric($value)) {
            return $value;
        }

        // For 'General' format code, we just pass the value although this is not entirely the way Excel does it,
        // it seems to round numbers to a total of 10 digits.
        if (($format === NumberFormat::FORMAT_GENERAL) || ($format === NumberFormat::FORMAT_TEXT)) {
            return $value;
        }

        $format = preg_replace_callback(
            '/(["])(?:(?=(\\\\?))\\2.)*?\\1/u',
            function ($matches) {
                return str_replace('.', chr(0x00), $matches[0]);
            },
            $format
        );

        // Convert any other escaped characters to quoted strings, e.g. (\T to "T")
        $format = preg_replace('/(\\\(((.)(?!((AM\/PM)|(A\/P))))|([^ ])))(?=(?:[^"]|"[^"]*")*$)/ui', '"${2}"', $format);

        // Get the sections, there can be up to four sections, separated with a semi-colon (but only if not a quoted literal)
        $sections = preg_split('/(;)(?=(?:[^"]|"[^"]*")*$)/u', $format);

        [$colors, $format, $value] = self::splitFormat($sections, $value);

        // In Excel formats, "_" is used to add spacing,
        //    The following character indicates the size of the spacing, which we can't do in HTML, so we just use a standard space
        $format = preg_replace('/_(.)/ui', ' ${1}', $format);

        // Let's begin inspecting the format and converting the value to a formatted string

        //  Check for date/time characters (not inside quotes)
        if (preg_match('/(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy](?=(?:[^"]|"[^"]*")*$)/miu', $format, $matches)) {
            // datetime format
            self::formatAsDate($value, $format);
        } else {
            if (substr($format, 0, 1) === '"' && substr($format, -1, 1) === '"') {
                $value = substr($format, 1, -1);
            } elseif (preg_match('/%$/', $format)) {
                // % number format
                self::formatAsPercentage($value, $format);
            } else {
                $value = self::formatAsNumber($value, $format);
            }
        }

        // Additional formatting provided by callback function
        if ($callBack !== null) {
            [$writerInstance, $function] = $callBack;
            $value = $writerInstance->$function($value, $colors);
        }

        $value = str_replace(chr(0x00), '.', $value);

        return $value;
    }
}
