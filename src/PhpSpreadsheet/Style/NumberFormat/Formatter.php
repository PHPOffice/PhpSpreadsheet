<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Formatter
{
    private const SYMBOL_AT = '/@(?=(?:[^"]*"[^"]*")*[^"]*\Z)/miu';
    private const SECTION_SPLIT = '/;(?=(?:[^"]*"[^"]*")*[^"]*\Z)/miu';

    /**
     * @param mixed $value
     * @param mixed $val
     * @param mixed $dfval
     */
    private static function splitFormatCompare($value, ?string $cond, $val, string $dfcond, $dfval): bool
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

    /** @param mixed $value */
    private static function splitFormat(array $sections, $value): array
    {
        // Extract the relevant section depending on whether number is positive, negative, or zero?
        // Text not supported yet.
        // Here is how the sections apply to various values in Excel:
        //   1 section:   [POSITIVE/NEGATIVE/ZERO/TEXT]
        //   2 sections:  [POSITIVE/ZERO/TEXT] [NEGATIVE]
        //   3 sections:  [POSITIVE/TEXT] [NEGATIVE] [ZERO]
        //   4 sections:  [POSITIVE] [NEGATIVE] [ZERO] [TEXT]
        $cnt = count($sections);
        $color_regex = '/\\[(' . implode('|', Color::NAMED_COLORS) . ')\\]/mui';
        $cond_regex = '/\\[(>|>=|<|<=|=|<>)([+-]?\\d+([.]\\d+)?)\\]/';
        $colors = ['', '', '', '', ''];
        $condops = ['', '', '', '', ''];
        $condvals = [0, 0, 0, 0, 0];
        for ($idx = 0; $idx < $cnt; ++$idx) {
            if (preg_match($color_regex, $sections[$idx], $matches)) {
                $colors[$idx] = $matches[0];
                $sections[$idx] = (string) preg_replace($color_regex, '', $sections[$idx]);
            }
            if (preg_match($cond_regex, $sections[$idx], $matches)) {
                $condops[$idx] = $matches[1];
                $condvals[$idx] = $matches[2];
                $sections[$idx] = (string) preg_replace($cond_regex, '', $sections[$idx]);
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
     * @param null|bool|float|int|RichText|string $value Value to format
     * @param string $format Format code, see = NumberFormat::FORMAT_*
     * @param array $callBack Callback function for additional formatting of string
     *
     * @return string Formatted string
     */
    public static function toFormattedString($value, $format, $callBack = null)
    {
        if (is_bool($value)) {
            return $value ? Calculation::getTRUE() : Calculation::getFALSE();
        }
        // For now we do not treat strings in sections, although section 4 of a format code affects strings
        // Process a single block format code containing @ for text substitution
        if (preg_match(self::SECTION_SPLIT, $format) === 0 && preg_match(self::SYMBOL_AT, $format) === 1) {
            return str_replace('"', '', preg_replace(self::SYMBOL_AT, (string) $value, $format) ?? '');
        }

        // If we have a text value, return it "as is"
        if (!is_numeric($value)) {
            return (string) $value;
        }

        // For 'General' format code, we just pass the value although this is not entirely the way Excel does it,
        // it seems to round numbers to a total of 10 digits.
        if (($format === NumberFormat::FORMAT_GENERAL) || ($format === NumberFormat::FORMAT_TEXT)) {
            return (string) $value;
        }

        // Ignore square-$-brackets prefix in format string, like "[$-411]ge.m.d", "[$-010419]0%", etc
        $format = (string) preg_replace('/^\[\$-[^\]]*\]/', '', $format);

        $format = (string) preg_replace_callback(
            '/(["])(?:(?=(\\\\?))\\2.)*?\\1/u',
            function ($matches) {
                return str_replace('.', chr(0x00), $matches[0]);
            },
            $format
        );

        // Convert any other escaped characters to quoted strings, e.g. (\T to "T")
        $format = (string) preg_replace('/(\\\(((.)(?!((AM\/PM)|(A\/P))))|([^ ])))(?=(?:[^"]|"[^"]*")*$)/ui', '"${2}"', $format);

        // Get the sections, there can be up to four sections, separated with a semi-colon (but only if not a quoted literal)
        $sections = preg_split(self::SECTION_SPLIT, $format) ?: [];

        [$colors, $format, $value] = self::splitFormat($sections, $value);

        // In Excel formats, "_" is used to add spacing,
        //    The following character indicates the size of the spacing, which we can't do in HTML, so we just use a standard space
        $format = (string) preg_replace('/_.?/ui', ' ', $format);

        // Let's begin inspecting the format and converting the value to a formatted string

        //  Check for date/time characters (not inside quotes)
        if (preg_match('/(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy](?=(?:[^"]|"[^"]*")*$)/miu', $format, $matches)) {
            // datetime format
            $value = DateFormatter::format($value, $format);
        } else {
            if (substr($format, 0, 1) === '"' && substr($format, -1, 1) === '"' && substr_count($format, '"') === 2) {
                $value = substr($format, 1, -1);
            } elseif (preg_match('/[0#, ]%/', $format)) {
                // % number format - avoid weird '-0' problem
                $value = PercentageFormatter::format(0 + (float) $value, $format);
            } else {
                $value = NumberFormatter::format($value, $format);
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
