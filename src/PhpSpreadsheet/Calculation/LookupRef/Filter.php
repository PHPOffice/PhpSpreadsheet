<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Filter
{
    /**
     * @param mixed $lookupArray
     * @param mixed $matchArray
     * @param mixed $ifEmpty
     *
     * @return mixed
     */
    public static function filter($lookupArray, $matchArray, $ifEmpty = null)
    {
        if (!is_array($matchArray)) {
            return ExcelError::VALUE();
        }

        $matchArray = self::enumerateArrayKeys($matchArray);

        $result = (Matrix::isColumnVector($matchArray))
            ? self::filterByRow($lookupArray, $matchArray)
            : self::filterByColumn($lookupArray, $matchArray);

        if (empty($result)) {
            return $ifEmpty ?? ExcelError::CALC();
        }

        return array_values(array_map('array_values', $result));
    }

    private static function enumerateArrayKeys(array $sortArray): array
    {
        array_walk(
            $sortArray,
            function (&$columns): void {
                if (is_array($columns)) {
                    $columns = array_values($columns);
                }
            }
        );

        return array_values($sortArray);
    }

    private static function filterByRow(array $lookupArray, array $matchArray): array
    {
        $matchArray = array_values(array_column($matchArray, 0));

        return array_filter(
            array_values($lookupArray),
            function ($index) use ($matchArray): bool {
                return (bool) $matchArray[$index];
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    private static function filterByColumn(array $lookupArray, array $matchArray): array
    {
        $lookupArray = Matrix::transpose($lookupArray);

        if (count($matchArray) === 1) {
            $matchArray = array_pop($matchArray);
        }

        array_walk(
            $matchArray,
            function (&$value): void {
                $value = [$value];
            }
        );

        $result = self::filterByRow($lookupArray, $matchArray);

        return Matrix::transpose($result);
    }
}
