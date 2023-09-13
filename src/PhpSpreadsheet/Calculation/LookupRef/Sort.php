<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Sort extends LookupRefValidations
{
    public const ORDER_ASCENDING = 1;
    public const ORDER_DESCENDING = -1;

    /**
     * SORT
     * The SORT function returns a sorted array of the elements in an array.
     * The returned array is the same shape as the provided array argument.
     * Both $sortIndex and $sortOrder can be arrays, to provide multi-level sorting.
     *
     * @param mixed $sortArray The range of cells being sorted
     * @param mixed $sortIndex The column or row number within the sortArray to sort on
     * @param mixed $sortOrder Flag indicating whether to sort ascending or descending
     *                          Ascending = 1 (self::ORDER_ASCENDING)
     *                          Descending = -1 (self::ORDER_DESCENDING)
     * @param mixed $byColumn Whether the sort should be determined by row (the default) or by column
     *
     * @return mixed The sorted values from the sort range
     */
    public static function sort(mixed $sortArray, mixed $sortIndex = 1, mixed $sortOrder = self::ORDER_ASCENDING, mixed $byColumn = false)
    {
        if (!is_array($sortArray)) {
            // Scalars are always returned "as is"
            return $sortArray;
        }

        $sortArray = self::enumerateArrayKeys($sortArray);

        $byColumn = (bool) $byColumn;
        $lookupIndexSize = $byColumn ? count($sortArray) : count($sortArray[0]);

        try {
            // If $sortIndex and $sortOrder are scalars, then convert them into arrays
            if (is_scalar($sortIndex)) {
                $sortIndex = [$sortIndex];
                $sortOrder = is_scalar($sortOrder) ? [$sortOrder] : $sortOrder;
            }
            // but the values of those array arguments still need validation
            $sortOrder = (empty($sortOrder) ? [self::ORDER_ASCENDING] : $sortOrder);
            self::validateArrayArgumentsForSort($sortIndex, $sortOrder, $lookupIndexSize);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // We want a simple, enumrated array of arrays where we can reference column by its index number.
        $sortArray = array_values(array_map('array_values', $sortArray));

        return ($byColumn === true)
            ? self::sortByColumn($sortArray, $sortIndex, $sortOrder)
            : self::sortByRow($sortArray, $sortIndex, $sortOrder);
    }

    /**
     * SORTBY
     * The SORTBY function sorts the contents of a range or array based on the values in a corresponding range or array.
     * The returned array is the same shape as the provided array argument.
     * Both $sortIndex and $sortOrder can be arrays, to provide multi-level sorting.
     *
     * @param mixed $sortArray The range of cells being sorted
     * @param mixed $args
     *              At least one additional argument must be provided, The vector or range to sort on
     *              After that, arguments are passed as pairs:
     *                    sort order: ascending or descending
     *                         Ascending = 1 (self::ORDER_ASCENDING)
     *                         Descending = -1 (self::ORDER_DESCENDING)
     *                    additional arrays or ranges for multi-level sorting
     *
     * @return mixed The sorted values from the sort range
     */
    public static function sortBy(mixed $sortArray, mixed ...$args)
    {
        if (!is_array($sortArray)) {
            // Scalars are always returned "as is"
            return $sortArray;
        }

        $sortArray = self::enumerateArrayKeys($sortArray);

        $lookupArraySize = count($sortArray);
        $argumentCount = count($args);

        try {
            $sortBy = $sortOrder = [];
            for ($i = 0; $i < $argumentCount; $i += 2) {
                $sortBy[] = self::validateSortVector($args[$i], $lookupArraySize);
                $sortOrder[] = self::validateSortOrder($args[$i + 1] ?? self::ORDER_ASCENDING);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::processSortBy($sortArray, $sortBy, $sortOrder);
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

    private static function validateScalarArgumentsForSort(mixed &$sortIndex, mixed &$sortOrder, int $sortArraySize): void
    {
        if (is_array($sortIndex) || is_array($sortOrder)) {
            throw new Exception(ExcelError::VALUE());
        }

        $sortIndex = self::validatePositiveInt($sortIndex, false);

        if ($sortIndex > $sortArraySize) {
            throw new Exception(ExcelError::VALUE());
        }

        $sortOrder = self::validateSortOrder($sortOrder);
    }

    private static function validateSortVector(mixed $sortVector, int $sortArraySize): array
    {
        if (!is_array($sortVector)) {
            throw new Exception(ExcelError::VALUE());
        }

        // It doesn't matter if it's a row or a column vectors, it works either way
        $sortVector = Functions::flattenArray($sortVector);
        if (count($sortVector) !== $sortArraySize) {
            throw new Exception(ExcelError::VALUE());
        }

        return $sortVector;
    }

    private static function validateSortOrder(mixed $sortOrder): int
    {
        $sortOrder = self::validateInt($sortOrder);
        if (($sortOrder == self::ORDER_ASCENDING || $sortOrder === self::ORDER_DESCENDING) === false) {
            throw new Exception(ExcelError::VALUE());
        }

        return $sortOrder;
    }

    /**
     * @param array $sortIndex
     */
    private static function validateArrayArgumentsForSort(&$sortIndex, mixed &$sortOrder, int $sortArraySize): void
    {
        // It doesn't matter if they're row or column vectors, it works either way
        $sortIndex = Functions::flattenArray($sortIndex);
        $sortOrder = Functions::flattenArray($sortOrder);

        if (
            count($sortOrder) === 0 || count($sortOrder) > $sortArraySize
            || (count($sortOrder) > count($sortIndex))
        ) {
            throw new Exception(ExcelError::VALUE());
        }

        if (count($sortIndex) > count($sortOrder)) {
            // If $sortOrder has fewer elements than $sortIndex, then the last order element is repeated.
            $sortOrder = array_merge(
                $sortOrder,
                array_fill(0, count($sortIndex) - count($sortOrder), array_pop($sortOrder))
            );
        }

        foreach ($sortIndex as $key => &$value) {
            self::validateScalarArgumentsForSort($value, $sortOrder[$key], $sortArraySize);
        }
    }

    private static function prepareSortVectorValues(array $sortVector): array
    {
        // Strings should be sorted case-insensitive; with booleans converted to locale-strings
        return array_map(
            function ($value) {
                if (is_bool($value)) {
                    return ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
                } elseif (is_string($value)) {
                    return StringHelper::strToLower($value);
                }

                return $value;
            },
            $sortVector
        );
    }

    /**
     * @param array[] $sortIndex
     * @param int[] $sortOrder
     */
    private static function processSortBy(array $sortArray, array $sortIndex, array $sortOrder): array
    {
        $sortArguments = [];
        $sortData = [];
        foreach ($sortIndex as $index => $sortValues) {
            $sortData[] = $sortValues;
            $sortArguments[] = self::prepareSortVectorValues($sortValues);
            $sortArguments[] = $sortOrder[$index] === self::ORDER_ASCENDING ? SORT_ASC : SORT_DESC;
        }

        $sortVector = self::executeVectorSortQuery($sortData, $sortArguments);

        return self::sortLookupArrayFromVector($sortArray, $sortVector);
    }

    /**
     * @param int[] $sortIndex
     * @param int[] $sortOrder
     */
    private static function sortByRow(array $sortArray, array $sortIndex, array $sortOrder): array
    {
        $sortVector = self::buildVectorForSort($sortArray, $sortIndex, $sortOrder);

        return self::sortLookupArrayFromVector($sortArray, $sortVector);
    }

    /**
     * @param int[] $sortIndex
     * @param int[] $sortOrder
     */
    private static function sortByColumn(array $sortArray, array $sortIndex, array $sortOrder): array
    {
        $sortArray = Matrix::transpose($sortArray);
        $result = self::sortByRow($sortArray, $sortIndex, $sortOrder);

        return Matrix::transpose($result);
    }

    /**
     * @param int[] $sortIndex
     * @param int[] $sortOrder
     */
    private static function buildVectorForSort(array $sortArray, array $sortIndex, array $sortOrder): array
    {
        $sortArguments = [];
        $sortData = [];
        foreach ($sortIndex as $index => $sortIndexValue) {
            $sortValues = array_column($sortArray, $sortIndexValue - 1);
            $sortData[] = $sortValues;
            $sortArguments[] = self::prepareSortVectorValues($sortValues);
            $sortArguments[] = $sortOrder[$index] === self::ORDER_ASCENDING ? SORT_ASC : SORT_DESC;
        }

        $sortData = self::executeVectorSortQuery($sortData, $sortArguments);

        return $sortData;
    }

    private static function executeVectorSortQuery(array $sortData, array $sortArguments): array
    {
        $sortData = Matrix::transpose($sortData);

        // We need to set an index that can be retained, as array_multisort doesn't maintain numeric keys.
        $sortDataIndexed = [];
        foreach ($sortData as $key => $value) {
            $sortDataIndexed[Coordinate::stringFromColumnIndex($key + 1)] = $value;
        }
        unset($sortData);

        $sortArguments[] = &$sortDataIndexed;

        array_multisort(...$sortArguments);

        // After the sort, we restore the numeric keys that will now be in the correct, sorted order
        $sortedData = [];
        foreach (array_keys($sortDataIndexed) as $key) {
            $sortedData[] = Coordinate::columnIndexFromString($key) - 1;
        }

        return $sortedData;
    }

    private static function sortLookupArrayFromVector(array $sortArray, array $sortVector): array
    {
        // Building a new array in the correct (sorted) order works; but may be memory heavy for larger arrays
        $sortedArray = [];
        foreach ($sortVector as $index) {
            $sortedArray[] = $sortArray[$index];
        }

        return $sortedArray;

//        uksort(
//            $lookupArray,
//            function (int $a, int $b) use (array $sortVector) {
//                return $sortVector[$a] <=> $sortVector[$b];
//            }
//        );
//
//        return $lookupArray;
    }
}
