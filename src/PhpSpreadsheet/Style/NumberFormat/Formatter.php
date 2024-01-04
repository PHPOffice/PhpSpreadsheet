<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color\BIFF8;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Formatter extends BaseFormatter
{
    /**
     * Matches any @ symbol that isn't enclosed in quotes.
     */
    private const SYMBOL_AT = '/@(?=(?:[^"]*"[^"]*")*[^"]*\Z)/miu';

    /**
     * Matches any ; symbol that isn't enclosed in quotes, for a "section" split.
     */
    private const SECTION_SPLIT = '/;(?=(?:[^"]*"[^"]*")*[^"]*\Z)/miu';

    private static function splitFormatComparison(
        mixed $value,
        ?string $condition,
        mixed $comparisonValue,
        string $defaultCondition,
        mixed $defaultComparisonValue
    ): bool {
        if (!$condition) {
            $condition = $defaultCondition;
            $comparisonValue = $defaultComparisonValue;
        }

        return match ($condition) {
            '>' => $value > $comparisonValue,
            '<' => $value < $comparisonValue,
            '<=' => $value <= $comparisonValue,
            '<>' => $value != $comparisonValue,
            '=' => $value == $comparisonValue,
            default => $value >= $comparisonValue,
        };
    }

    private static function splitFormatForSectionSelection(array $sections, mixed $value): array
    {
        // Extract the relevant section depending on whether number is positive, negative, or zero?
        // Text not supported yet.
        // Here is how the sections apply to various values in Excel:
        //   1 section:   [POSITIVE/NEGATIVE/ZERO/TEXT]
        //   2 sections:  [POSITIVE/ZERO/TEXT] [NEGATIVE]
        //   3 sections:  [POSITIVE/TEXT] [NEGATIVE] [ZERO]
        //   4 sections:  [POSITIVE] [NEGATIVE] [ZERO] [TEXT]
        $sectionCount = count($sections);
        // Colour could be a named colour, or a numeric index entry in the colour-palette
        $color_regex = '/\\[(' . implode('|', Color::NAMED_COLORS) . '|color\\s*(\\d+))\\]/mui';
        $cond_regex = '/\\[(>|>=|<|<=|=|<>)([+-]?\\d+([.]\\d+)?)\\]/';
        $colors = ['', '', '', '', ''];
        $conditionOperations = ['', '', '', '', ''];
        $conditionComparisonValues = [0, 0, 0, 0, 0];
        for ($idx = 0; $idx < $sectionCount; ++$idx) {
            if (preg_match($color_regex, $sections[$idx], $matches)) {
                if (isset($matches[2])) {
                    $colors[$idx] = '#' . BIFF8::lookup((int) $matches[2] + 7)['rgb'];
                } else {
                    $colors[$idx] = $matches[0];
                }
                $sections[$idx] = (string) preg_replace($color_regex, '', $sections[$idx]);
            }
            if (preg_match($cond_regex, $sections[$idx], $matches)) {
                $conditionOperations[$idx] = $matches[1];
                $conditionComparisonValues[$idx] = $matches[2];
                $sections[$idx] = (string) preg_replace($cond_regex, '', $sections[$idx]);
            }
        }
        $color = $colors[0];
        $format = $sections[0];
        $absval = $value;
        switch ($sectionCount) {
            case 2:
                $absval = abs($value);
                if (!self::splitFormatComparison($value, $conditionOperations[0], $conditionComparisonValues[0], '>=', 0)) {
                    $color = $colors[1];
                    $format = $sections[1];
                }

                break;
            case 3:
            case 4:
                $absval = abs($value);
                if (!self::splitFormatComparison($value, $conditionOperations[0], $conditionComparisonValues[0], '>', 0)) {
                    if (self::splitFormatComparison($value, $conditionOperations[1], $conditionComparisonValues[1], '<', 0)) {
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
     * @param string $format Format code: see = self::FORMAT_* for predefined values;
     *                          or can be any valid MS Excel custom format string
     * @param ?array $callBack Callback function for additional formatting of string
     *
     * @return string Formatted string
     */
    public static function toFormattedString($value, string $format, ?array $callBack = null): string
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
            return self::adjustSeparators((string) $value);
        }

        // Ignore square-$-brackets prefix in format string, like "[$-411]ge.m.d", "[$-010419]0%", etc
        $format = (string) preg_replace('/^\[\$-[^\]]*\]/', '', $format);

        $format = (string) preg_replace_callback(
            '/(["])(?:(?=(\\\\?))\\2.)*?\\1/u',
            fn (array $matches): string => str_replace('.', chr(0x00), $matches[0]),
            $format
        );

        // Convert any other escaped characters to quoted strings, e.g. (\T to "T")
        $format = (string) preg_replace('/(\\\(((.)(?!((AM\/PM)|(A\/P))))|([^ ])))(?=(?:[^"]|"[^"]*")*$)/ui', '"${2}"', $format);

        // Get the sections, there can be up to four sections, separated with a semi-colon (but only if not a quoted literal)
        $sections = preg_split(self::SECTION_SPLIT, $format) ?: [];

        [$colors, $format, $value] = self::splitFormatForSectionSelection($sections, $value);

        // In Excel formats, "_" is used to add spacing,
        //    The following character indicates the size of the spacing, which we can't do in HTML, so we just use a standard space
        $format = (string) preg_replace('/_.?/ui', ' ', $format);

        // Let's begin inspecting the format and converting the value to a formatted string
        if (
            //  Check for date/time characters (not inside quotes)
            (preg_match('/(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy](?=(?:[^"]|"[^"]*")*$)/miu', $format))
            // A date/time with a decimal time shouldn't have a digit placeholder before the decimal point
            && (preg_match('/[0\?#]\.(?![^\[]*\])/miu', $format) === 0)
        ) {
            // datetime format
            $value = DateFormatter::format($value, $format);
        } else {
            if (str_starts_with($format, '"') && str_ends_with($format, '"') && substr_count($format, '"') === 2) {
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

        return str_replace(chr(0x00), '.', $value);
    }
}
