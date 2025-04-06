<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Text
{
    use ArrayEnabled;

    /**
     * LEN.
     *
     * @param mixed $value String Value
     *                         Or can be an array of values
     *
     * @return array|int|string If an array of values is passed for the argument, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function length(mixed $value = ''): array|int|string
    {
        if (is_array($value)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $value);
        }

        try {
            $value = Helpers::extractString($value, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return mb_strlen($value, 'UTF-8');
    }

    /**
     * Compares two text strings and returns TRUE if they are exactly the same, FALSE otherwise.
     * EXACT is case-sensitive but ignores formatting differences.
     * Use EXACT to test text being entered into a document.
     *
     * @param mixed $value1 String Value
     *                         Or can be an array of values
     * @param mixed $value2 String Value
     *                         Or can be an array of values
     *
     * @return array|bool|string If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function exact(mixed $value1, mixed $value2): array|bool|string
    {
        if (is_array($value1) || is_array($value2)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value1, $value2);
        }

        try {
            $value1 = Helpers::extractString($value1, true);
            $value2 = Helpers::extractString($value2, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return $value2 === $value1;
    }

    /**
     * T.
     *
     * @param mixed $testValue Value to check
     *                         Or can be an array of values
     *
     * @return array|string If an array of values is passed for the argument, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function test(mixed $testValue = ''): array|string
    {
        if (is_array($testValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $testValue);
        }

        if (is_string($testValue)) {
            return $testValue;
        }

        return '';
    }

    /**
     * TEXTSPLIT.
     *
     * @param mixed $text the text that you're searching
     * @param null|array|string $columnDelimiter The text that marks the point where to spill the text across columns.
     *                          Multiple delimiters can be passed as an array of string values
     * @param null|array|string $rowDelimiter The text that marks the point where to spill the text down rows.
     *                          Multiple delimiters can be passed as an array of string values
     * @param bool $ignoreEmpty Specify FALSE to create an empty cell when two delimiters are consecutive.
     *                              true = create empty cells
     *                              false = skip empty cells
     *                              Defaults to TRUE, which creates an empty cell
     * @param bool $matchMode Determines whether the match is case-sensitive or not.
     *                              true = case-sensitive
     *                              false = case-insensitive
     *                         By default, a case-sensitive match is done.
     * @param mixed $padding The value with which to pad the result.
     *                              The default is #N/A.
     *
     * @return array|string the array built from the text, split by the row and column delimiters, or an error string
     */
    public static function split(mixed $text, $columnDelimiter = null, $rowDelimiter = null, bool $ignoreEmpty = false, bool $matchMode = true, mixed $padding = '#N/A'): array|string
    {
        $text = Functions::flattenSingleValue($text);
        if (ErrorValue::isError($text, true)) {
            return StringHelper::convertToString($text);
        }

        $flags = self::matchFlags($matchMode);

        if ($rowDelimiter !== null) {
            $delimiter = self::buildDelimiter($rowDelimiter);
            $rows = ($delimiter === '()')
                ? [$text]
                : Preg::split("/{$delimiter}/{$flags}", StringHelper::convertToString($text));
        } else {
            $rows = [$text];
        }

        if ($ignoreEmpty === true) {
            $rows = array_values(array_filter(
                $rows,
                fn ($row): bool => $row !== ''
            ));
        }

        if ($columnDelimiter !== null) {
            $delimiter = self::buildDelimiter($columnDelimiter);
            array_walk(
                $rows,
                function (&$row) use ($delimiter, $flags, $ignoreEmpty): void {
                    $row = ($delimiter === '()')
                        ? [$row]
                        : Preg::split("/{$delimiter}/{$flags}", StringHelper::convertToString($row));
                    if ($ignoreEmpty === true) {
                        $row = array_values(array_filter(
                            $row,
                            fn ($value): bool => $value !== ''
                        ));
                    }
                }
            );
            if ($ignoreEmpty === true) {
                $rows = array_values(array_filter(
                    $rows,
                    fn ($row): bool => $row !== [] && $row !== ['']
                ));
            }
        }

        return self::applyPadding($rows, $padding);
    }

    private static function applyPadding(array $rows, mixed $padding): array
    {
        $columnCount = array_reduce(
            $rows,
            fn (int $counter, array $row): int => max($counter, count($row)),
            0
        );

        return array_map(
            fn (array $row): array => (count($row) < $columnCount)
                    ? array_merge($row, array_fill(0, $columnCount - count($row), $padding))
                    : $row,
            $rows
        );
    }

    /**
     * @param null|array|string $delimiter the text that marks the point before which you want to split
     *                                 Multiple delimiters can be passed as an array of string values
     */
    private static function buildDelimiter($delimiter): string
    {
        $valueSet = Functions::flattenArray($delimiter);

        if (is_array($delimiter) && count($valueSet) > 1) {
            $quotedDelimiters = array_map(
                fn ($delimiter): string => preg_quote($delimiter ?? '', '/'),
                $valueSet
            );
            $delimiters = implode('|', $quotedDelimiters);

            return '(' . $delimiters . ')';
        }

        return '(' . preg_quote(StringHelper::convertToString(Functions::flattenSingleValue($delimiter)), '/') . ')';
    }

    private static function matchFlags(bool $matchMode): string
    {
        return ($matchMode === true) ? 'miu' : 'mu';
    }

    public static function fromArray(array $array, int $format = 0): string
    {
        $result = [];
        foreach ($array as $row) {
            $cells = [];
            foreach ($row as $cellValue) {
                $value = ($format === 1) ? self::formatValueMode1($cellValue) : self::formatValueMode0($cellValue);
                $cells[] = $value;
            }
            $result[] = implode(($format === 1) ? ',' : ', ', $cells);
        }

        $result = implode(($format === 1) ? ';' : ', ', $result);

        return ($format === 1) ? '{' . $result . '}' : $result;
    }

    private static function formatValueMode0(mixed $cellValue): string
    {
        if (is_bool($cellValue)) {
            return Calculation::getLocaleBoolean($cellValue ? 'TRUE' : 'FALSE');
        }

        return StringHelper::convertToString($cellValue);
    }

    private static function formatValueMode1(mixed $cellValue): string
    {
        if (is_string($cellValue) && ErrorValue::isError($cellValue) === false) {
            return Calculation::FORMULA_STRING_QUOTE . $cellValue . Calculation::FORMULA_STRING_QUOTE;
        } elseif (is_bool($cellValue)) {
            return Calculation::getLocaleBoolean($cellValue ? 'TRUE' : 'FALSE');
        }

        return StringHelper::convertToString($cellValue);
    }
}
