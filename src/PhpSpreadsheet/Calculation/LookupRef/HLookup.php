<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class HLookup extends LookupBase
{
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
    public static function lookup($lookupValue, $lookupArray, $indexNumber, $notExactMatch = true)
    {
        $lookupValue = Functions::flattenSingleValue($lookupValue);
        $indexNumber = Functions::flattenSingleValue($indexNumber);
        $notExactMatch = Functions::flattenSingleValue($notExactMatch);

        try {
            $indexNumber = self::validateIndexLookup($lookupArray, $indexNumber);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $f = array_keys($lookupArray);
        $firstRow = reset($f);
        if ((!is_array($lookupArray[$firstRow])) || ($indexNumber > count($lookupArray))) {
            return Functions::REF();
        }

        $firstkey = $f[0] - 1;
        $returnColumn = $firstkey + $indexNumber;
        $firstColumn = array_shift($f);
        $rowNumber = self::hLookupSearch($lookupValue, $lookupArray, $firstColumn, $notExactMatch);

        if ($rowNumber !== null) {
            //  otherwise return the appropriate value
            return $lookupArray[$returnColumn][$rowNumber];
        }

        return Functions::NA();
    }

    private static function hLookupSearch($lookupValue, $lookupArray, $column, $notExactMatch)
    {
        $lookupLower = StringHelper::strToLower($lookupValue);

        $rowNumber = null;
        foreach ($lookupArray[$column] as $rowKey => $rowData) {
            // break if we have passed possible keys
            $bothNumeric = is_numeric($lookupValue) && is_numeric($rowData);
            $bothNotNumeric = !is_numeric($lookupValue) && !is_numeric($rowData);
            $cellDataLower = StringHelper::strToLower($rowData);

            if (
                $notExactMatch &&
                (($bothNumeric && $rowData > $lookupValue) || ($bothNotNumeric && $cellDataLower > $lookupLower))
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
}
