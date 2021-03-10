<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Lookup
{
    private static function validateIndexLookup($lookup_array, $index_number)
    {
        // index_number must be a number greater than or equal to 1
        if (!is_numeric($index_number) || $index_number < 1) {
            throw new Exception(Functions::VALUE());
        }

        // index_number must be less than or equal to the number of columns in lookup_array
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            throw new Exception(Functions::REF());
        }

        return (int) $index_number;
    }

    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value
     *     in the same row based on the index_number.
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupArray The range of cells being searched
     * @param mixed $indexNumber The column number in table_array from which the matching value must be returned.
     *                                The first column is 1.
     * @param mixed $notExactMatch determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function VLOOKUP($lookupValue, $lookupArray, $indexNumber, $notExactMatch = true)
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
        $firstRow = array_pop($f);
        if ((!is_array($lookupArray[$firstRow])) || ($indexNumber > count($lookupArray[$firstRow]))) {
            return Functions::REF();
        }
        $columnKeys = array_keys($lookupArray[$firstRow]);
        $returnColumn = $columnKeys[--$indexNumber];
        $firstColumn = array_shift($columnKeys);

        if (!$notExactMatch) {
            uasort($lookupArray, ['self', 'vlookupSort']);
        }

        $rowNumber = self::vLookupSearch($lookupValue, $lookupArray, $firstColumn, $notExactMatch);

        if ($rowNumber !== null) {
            // return the appropriate value
            return $lookupArray[$rowNumber][$returnColumn];
        }

        return Functions::NA();
    }

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
    public static function HLOOKUP($lookupValue, $lookupArray, $indexNumber, $notExactMatch = true)
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

    /**
     * LOOKUP
     * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupVector The range of cells being searched
     * @param null|mixed $resultVector The column from which the matching value must be returned
     *
     * @return mixed The value of the found cell
     */
    public static function LOOKUP($lookupValue, $lookupVector, $resultVector = null)
    {
        $lookupValue = Functions::flattenSingleValue($lookupValue);

        if (!is_array($lookupVector)) {
            return Functions::NA();
        }
        $hasResultVector = isset($resultVector);
        $lookupRows = count($lookupVector);
        $l = array_keys($lookupVector);
        $l = array_shift($l);
        $lookupColumns = count($lookupVector[$l]);
        // we correctly orient our results
        if (($lookupRows === 1 && $lookupColumns > 1) || (!$hasResultVector && $lookupRows === 2 && $lookupColumns !== 2)) {
            $lookupVector = LookupRef::TRANSPOSE($lookupVector);
            $lookupRows = count($lookupVector);
            $l = array_keys($lookupVector);
            $lookupColumns = count($lookupVector[array_shift($l)]);
        }

        if ($resultVector === null) {
            $resultVector = $lookupVector;
        }
        $resultRows = count($resultVector);
        $l = array_keys($resultVector);
        $l = array_shift($l);
        $resultColumns = count($resultVector[$l]);
        // we correctly orient our results
        if ($resultRows === 1 && $resultColumns > 1) {
            $resultVector = LookupRef::TRANSPOSE($resultVector);
        }

        if ($lookupRows === 2 && !$hasResultVector) {
            $resultVector = array_pop($lookupVector);
            $lookupVector = array_shift($lookupVector);
        }

        if ($lookupColumns !== 2) {
            foreach ($lookupVector as &$value) {
                if (is_array($value)) {
                    $k = array_keys($value);
                    $key1 = $key2 = array_shift($k);
                    ++$key2;
                    $dataValue1 = $value[$key1];
                } else {
                    $key1 = 0;
                    $key2 = 1;
                    $dataValue1 = $value;
                }
                $dataValue2 = array_shift($resultVector);
                if (is_array($dataValue2)) {
                    $dataValue2 = array_shift($dataValue2);
                }
                $value = [$key1 => $dataValue1, $key2 => $dataValue2];
            }
            unset($value);
        }

        return self::VLOOKUP($lookupValue, $lookupVector, 2);
    }

    private static function vlookupSort($a, $b)
    {
        reset($a);
        $firstColumn = key($a);
        $aLower = StringHelper::strToLower($a[$firstColumn]);
        $bLower = StringHelper::strToLower($b[$firstColumn]);
        if ($aLower == $bLower) {
            return 0;
        }

        return ($aLower < $bLower) ? -1 : 1;
    }

    private static function vLookupSearch($lookupValue, $lookupArray, $column, $notExactMatch)
    {
        $lookupLower = StringHelper::strToLower($lookupValue);

        $rowNumber = null;
        foreach ($lookupArray as $rowKey => $rowData) {
            $bothNumeric = is_numeric($lookupValue) && is_numeric($rowData[$column]);
            $bothNotNumeric = !is_numeric($lookupValue) && !is_numeric($rowData[$column]);
            $firstLower = StringHelper::strToLower($rowData[$column]);

            // break if we have passed possible keys
            if (
                (is_numeric($lookupValue) && is_numeric($rowData[$column]) && ($rowData[$column] > $lookupValue)) ||
                (!is_numeric($lookupValue) && !is_numeric($rowData[$column]) && ($firstLower > $lookupLower))
            ) {
                break;
            }

            // remember the last key, but only if datatypes match
            if ($bothNumeric || $bothNotNumeric) {
                // Spreadsheets software returns first exact match,
                // we have sorted and we might have broken key orders
                // we want the first one (by its initial index)
                if ($notExactMatch) {
                    $rowNumber = $rowKey;
                } elseif (($firstLower == $lookupLower) && (($rowNumber == false) || ($rowKey < $rowNumber))) {
                    $rowNumber = $rowKey;
                }
            }
        }

        return $rowNumber;
    }

    private static function hLookupSearch($lookupValue, $lookupArray, $column, $notExactMatch)
    {
        $lookupLower = StringHelper::strToLower($lookupValue);

        $rowNumber = null;
        foreach ($lookupArray[$column] as $rowKey => $rowData) {
            // break if we have passed possible keys
            $bothNumeric = is_numeric($lookupValue) && is_numeric($rowData);
            $bothNotNumeric = !is_numeric($lookupValue) && !is_numeric($rowData);
            $rowDataLower = StringHelper::strToLower($rowData);

            if ($notExactMatch &&
                (($bothNumeric && $rowData > $lookupValue) || ($bothNotNumeric && $rowDataLower > $lookupLower))
            ) {
                break;
            }

            // Remember the last key, but only if datatypes match (as in VLOOKUP)
            if ($bothNumeric || $bothNotNumeric) {
                if ($notExactMatch) {
                    $rowNumber = $rowKey;
                } elseif ($rowDataLower === $lookupLower && ($rowNumber === null || $rowKey < $rowNumber)) {
                    $rowNumber = $rowKey;
                }
            }
        }

        return $rowNumber;
    }
}
