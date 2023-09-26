<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class HLookup extends LookupBase
{
    use ArrayEnabled;

    /**
     * HLOOKUP
     * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value
     *     in the same column based on the index_number.
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupArray The range of cells being searched
     * @param mixed $indexNumber The row number in table_array from which the matching value must be returned.
     *                                The first row is 1.
     * @param mixed $notExactMatch determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function lookup(mixed $lookupValue, mixed $lookupArray, mixed $indexNumber, mixed $notExactMatch = true)
    {
        if (is_array($lookupValue) || is_array($indexNumber)) {
            return self::evaluateArrayArgumentsIgnore([self::class, __FUNCTION__], 1, $lookupValue, $lookupArray, $indexNumber, $notExactMatch);
        }

        $notExactMatch = (bool) ($notExactMatch ?? true);

        try {
            self::validateLookupArray($lookupArray);
            $lookupArray = self::convertLiteralArray($lookupArray);
            $indexNumber = self::validateIndexLookup($lookupArray, $indexNumber);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $f = array_keys($lookupArray);
        $firstRow = reset($f);
        if ((!is_array($lookupArray[$firstRow])) || ($indexNumber > count($lookupArray))) {
            return ExcelError::REF();
        }

        $firstkey = $f[0] - 1;
        $returnColumn = $firstkey + $indexNumber;
        $firstColumn = array_shift($f) ?? 1;
        $rowNumber = self::hLookupSearch($lookupValue, $lookupArray, $firstColumn, $notExactMatch);

        if ($rowNumber !== null) {
            //  otherwise return the appropriate value
            return $lookupArray[$returnColumn][Coordinate::stringFromColumnIndex($rowNumber)];
        }

        return ExcelError::NA();
    }

    /**
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param  int|string $column
     */
    private static function hLookupSearch(mixed $lookupValue, array $lookupArray, $column, bool $notExactMatch): ?int
    {
        $lookupLower = StringHelper::strToLower((string) $lookupValue);

        $rowNumber = null;
        foreach ($lookupArray[$column] as $rowKey => $rowData) {
            // break if we have passed possible keys
            $bothNumeric = is_numeric($lookupValue) && is_numeric($rowData);
            $bothNotNumeric = !is_numeric($lookupValue) && !is_numeric($rowData);
            $cellDataLower = StringHelper::strToLower((string) $rowData);

            if (
                $notExactMatch
                && (($bothNumeric && $rowData > $lookupValue) || ($bothNotNumeric && $cellDataLower > $lookupLower))
            ) {
                break;
            }

            $rowNumber = self::checkMatch(
                $bothNumeric,
                $bothNotNumeric,
                $notExactMatch,
                Coordinate::columnIndexFromString($rowKey),
                $cellDataLower,
                $lookupLower,
                $rowNumber
            );
        }

        return $rowNumber;
    }

    private static function convertLiteralArray(array $lookupArray): array
    {
        if (array_key_exists(0, $lookupArray)) {
            $lookupArray2 = [];
            $row = 0;
            foreach ($lookupArray as $arrayVal) {
                ++$row;
                if (!is_array($arrayVal)) {
                    $arrayVal = [$arrayVal];
                }
                $arrayVal2 = [];
                foreach ($arrayVal as $key2 => $val2) {
                    $index = Coordinate::stringFromColumnIndex($key2 + 1);
                    $arrayVal2[$index] = $val2;
                }
                $lookupArray2[$row] = $arrayVal2;
            }
            $lookupArray = $lookupArray2;
        }

        return $lookupArray;
    }
}
