<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class VLookup extends LookupBase
{
    use ArrayEnabled;

    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value
     *     in the same row based on the index_number.
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed[] $lookupArray The range of cells being searched
     * @param array<mixed>|float|int|string $indexNumber The column number in table_array from which the matching value must be returned.
     *                                The first column is 1.
     * @param mixed $notExactMatch determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function lookup(mixed $lookupValue, $lookupArray, mixed $indexNumber, mixed $notExactMatch = true): mixed
    {
        if (is_array($lookupValue) || is_array($indexNumber)) {
            return self::evaluateArrayArgumentsIgnore([self::class, __FUNCTION__], 1, $lookupValue, $lookupArray, $indexNumber, $notExactMatch);
        }

        $notExactMatch = (bool) ($notExactMatch ?? true);

        try {
            self::validateLookupArray($lookupArray);
            $indexNumber = self::validateIndexLookup($lookupArray, $indexNumber);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $f = array_keys($lookupArray);
        $firstRow = array_pop($f);
        if ((!is_array($lookupArray[$firstRow])) || ($indexNumber > count($lookupArray[$firstRow]))) {
            return ExcelError::REF();
        }
        $columnKeys = array_keys($lookupArray[$firstRow]);
        $returnColumn = $columnKeys[--$indexNumber];
        $firstColumn = array_shift($columnKeys) ?? 1;

        if (!$notExactMatch) {
            /** @var callable $callable */
            $callable = [self::class, 'vlookupSort'];
            uasort($lookupArray, $callable);
        }

        /** @var string[][] $lookupArray */
        $rowNumber = self::vLookupSearch($lookupValue, $lookupArray, $firstColumn, $notExactMatch);

        if ($rowNumber !== null) {
            // return the appropriate value
            return $lookupArray[$rowNumber][$returnColumn];
        }

        return ExcelError::NA();
    }

    /**
     * @param scalar[] $a
     * @param scalar[] $b
     */
    private static function vlookupSort(array $a, array $b): int
    {
        reset($a);
        $firstColumn = key($a);
        $aLower = StringHelper::strToLower((string) $a[$firstColumn]);
        $bLower = StringHelper::strToLower((string) $b[$firstColumn]);

        if ($aLower == $bLower) {
            return 0;
        }

        return ($aLower < $bLower) ? -1 : 1;
    }

    /**
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param string[][] $lookupArray
     * @param  int|string $column
     */
    private static function vLookupSearch(mixed $lookupValue, array $lookupArray, $column, bool $notExactMatch): ?int
    {
        $lookupLower = StringHelper::strToLower(StringHelper::convertToString($lookupValue));

        $rowNumber = null;
        foreach ($lookupArray as $rowKey => $rowData) {
            $bothNumeric = self::numeric($lookupValue) && self::numeric($rowData[$column]);
            $bothNotNumeric = !self::numeric($lookupValue) && !self::numeric($rowData[$column]);
            $cellDataLower = StringHelper::strToLower((string) $rowData[$column]);

            // break if we have passed possible keys
            if (
                $notExactMatch
                && (($bothNumeric && ($rowData[$column] > $lookupValue))
                || ($bothNotNumeric && ($cellDataLower > $lookupLower)))
            ) {
                break;
            }

            $rowNumber = self::checkMatch(
                $bothNumeric,
                $bothNotNumeric,
                $notExactMatch,
                $rowKey,
                $cellDataLower,
                $lookupLower,
                $rowNumber
            );
        }

        return $rowNumber;
    }

    private static function numeric(mixed $value): bool
    {
        return is_int($value) || is_float($value);
    }
}
