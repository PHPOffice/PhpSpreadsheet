<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Unique
{
    /**
     * UNIQUE
     * The UNIQUE function searches for value either from a one-row or one-column range or from an array.
     *
     * @param mixed $lookupVector The range of cells being searched
     * @param mixed $byColumn Whether the uniqueness should be determined by row (the default) or by column
     * @param mixed $exactlyOnce Whether the function should return only entries that occur just once in the list
     *
     * @return mixed The unique values from the search range
     */
    public static function unique(mixed $lookupVector, mixed $byColumn = false, mixed $exactlyOnce = false)
    {
        if (!is_array($lookupVector)) {
            // Scalars are always returned "as is"
            return $lookupVector;
        }

        $byColumn = (bool) $byColumn;
        $exactlyOnce = (bool) $exactlyOnce;

        return ($byColumn === true)
            ? self::uniqueByColumn($lookupVector, $exactlyOnce)
            : self::uniqueByRow($lookupVector, $exactlyOnce);
    }

    /**
     * @return mixed
     */
    private static function uniqueByRow(array $lookupVector, bool $exactlyOnce)
    {
        // When not $byColumn, we count whole rows or values, not individual values
        //      so implode each row into a single string value
        array_walk(
            $lookupVector,
            function (array &$value): void {
                $value = implode(chr(0x00), $value);
            }
        );

        $result = self::countValuesCaseInsensitive($lookupVector);

        if ($exactlyOnce === true) {
            $result = self::exactlyOnceFilter($result);
        }

        if (count($result) === 0) {
            return ExcelError::CALC();
        }

        $result = array_keys($result);

        // restore rows from their strings
        array_walk(
            $result,
            function (string &$value): void {
                $value = explode(chr(0x00), $value);
            }
        );

        return (count($result) === 1) ? array_pop($result) : $result;
    }

    /**
     * @return mixed
     */
    private static function uniqueByColumn(array $lookupVector, bool $exactlyOnce)
    {
        $flattenedLookupVector = Functions::flattenArray($lookupVector);

        if (count($lookupVector, COUNT_RECURSIVE) > count($flattenedLookupVector, COUNT_RECURSIVE) + 1) {
            // We're looking at a full column check (multiple rows)
            $transpose = Matrix::transpose($lookupVector);
            $result = self::uniqueByRow($transpose, $exactlyOnce);

            return (is_array($result)) ? Matrix::transpose($result) : $result;
        }

        $result = self::countValuesCaseInsensitive($flattenedLookupVector);

        if ($exactlyOnce === true) {
            $result = self::exactlyOnceFilter($result);
        }

        if (count($result) === 0) {
            return ExcelError::CALC();
        }

        $result = array_keys($result);

        return $result;
    }

    private static function countValuesCaseInsensitive(array $caseSensitiveLookupValues): array
    {
        $caseInsensitiveCounts = array_count_values(
            array_map(
                fn (string $value): string => StringHelper::strToUpper($value),
                $caseSensitiveLookupValues
            )
        );

        $caseSensitiveCounts = [];
        foreach ($caseInsensitiveCounts as $caseInsensitiveKey => $count) {
            if (is_numeric($caseInsensitiveKey)) {
                $caseSensitiveCounts[$caseInsensitiveKey] = $count;
            } else {
                foreach ($caseSensitiveLookupValues as $caseSensitiveValue) {
                    if ($caseInsensitiveKey === StringHelper::strToUpper($caseSensitiveValue)) {
                        $caseSensitiveCounts[$caseSensitiveValue] = $count;

                        break;
                    }
                }
            }
        }

        return $caseSensitiveCounts;
    }

    private static function exactlyOnceFilter(array $values): array
    {
        return array_filter(
            $values,
            fn ($value): bool => $value === 1
        );
    }
}
