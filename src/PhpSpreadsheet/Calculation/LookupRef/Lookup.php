<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Lookup
{
    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value
     *     in the same row based on the index_number.
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The column number in table_array from which the matching value must be returned.
     *                                The first column is 1.
     * @param mixed $not_exact_match determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function VLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);
        $index_number = Functions::flattenSingleValue($index_number);
        $not_exact_match = Functions::flattenSingleValue($not_exact_match);

        // index_number must be greater than or equal to 1
        if ($index_number < 1) {
            return Functions::VALUE();
        }

        // index_number must be less than or equal to the number of columns in lookup_array
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            return Functions::REF();
        }
        $f = array_keys($lookup_array);
        $firstRow = array_pop($f);
        if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array[$firstRow]))) {
            return Functions::REF();
        }
        $columnKeys = array_keys($lookup_array[$firstRow]);
        $returnColumn = $columnKeys[--$index_number];
        $firstColumn = array_shift($columnKeys);

        if (!$not_exact_match) {
            uasort($lookup_array, ['self', 'vlookupSort']);
        }

        $lookupLower = StringHelper::strToLower($lookup_value);
        $rowNumber = $rowValue = false;
        foreach ($lookup_array as $rowKey => $rowData) {
            $firstLower = StringHelper::strToLower($rowData[$firstColumn]);

            // break if we have passed possible keys
            if (
                (is_numeric($lookup_value) && is_numeric($rowData[$firstColumn]) && ($rowData[$firstColumn] > $lookup_value)) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]) && ($firstLower > $lookupLower))
            ) {
                break;
            }
            // remember the last key, but only if datatypes match
            if (
                (is_numeric($lookup_value) && is_numeric($rowData[$firstColumn])) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]))
            ) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif (
                    ($firstLower == $lookupLower)
                    // Spreadsheets software returns first exact match,
                    // we have sorted and we might have broken key orders
                    // we want the first one (by its initial index)
                    && (($rowNumber == false) || ($rowKey < $rowNumber))
                ) {
                    $rowNumber = $rowKey;
                }
            }
        }

        if ($rowNumber !== false) {
            // return the appropriate value
            return $lookup_array[$rowNumber][$returnColumn];
        }

        return Functions::NA();
    }

    /**
     * HLOOKUP
     * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value
     *     in the same column based on the index_number.
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The row number in table_array from which the matching value must be returned.
     *                                The first row is 1.
     * @param mixed $not_exact_match determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function HLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);
        $index_number = Functions::flattenSingleValue($index_number);
        $not_exact_match = Functions::flattenSingleValue($not_exact_match);

        // index_number must be greater than or equal to 1
        if ($index_number < 1) {
            return Functions::VALUE();
        }

        // index_number must be less than or equal to the number of columns in lookup_array
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            return Functions::REF();
        }
        $f = array_keys($lookup_array);
        $firstRow = reset($f);
        if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array))) {
            return Functions::REF();
        }

        $firstkey = $f[0] - 1;
        $returnColumn = $firstkey + $index_number;
        $firstColumn = array_shift($f);
        $rowNumber = null;
        foreach ($lookup_array[$firstColumn] as $rowKey => $rowData) {
            // break if we have passed possible keys
            $bothNumeric = is_numeric($lookup_value) && is_numeric($rowData);
            $bothNotNumeric = !is_numeric($lookup_value) && !is_numeric($rowData);
            $lookupLower = StringHelper::strToLower($lookup_value);
            $rowDataLower = StringHelper::strToLower($rowData);

            if (
                $not_exact_match && (
                    ($bothNumeric && $rowData > $lookup_value) ||
                    ($bothNotNumeric && $rowDataLower > $lookupLower)
                )
            ) {
                break;
            }

            // Remember the last key, but only if datatypes match (as in VLOOKUP)
            if ($bothNumeric || $bothNotNumeric) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif (
                    $rowDataLower === $lookupLower
                    && ($rowNumber === null || $rowKey < $rowNumber)
                ) {
                    $rowNumber = $rowKey;
                }
            }
        }

        if ($rowNumber !== null) {
            //  otherwise return the appropriate value
            return $lookup_array[$returnColumn][$rowNumber];
        }

        return Functions::NA();
    }

    /**
     * LOOKUP
     * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_vector The range of cells being searched
     * @param null|mixed $result_vector The column from which the matching value must be returned
     *
     * @return mixed The value of the found cell
     */
    public static function LOOKUP($lookup_value, $lookup_vector, $result_vector = null)
    {
        $lookup_value = Functions::flattenSingleValue($lookup_value);

        if (!is_array($lookup_vector)) {
            return Functions::NA();
        }
        $hasResultVector = isset($result_vector);
        $lookupRows = count($lookup_vector);
        $l = array_keys($lookup_vector);
        $l = array_shift($l);
        $lookupColumns = count($lookup_vector[$l]);
        // we correctly orient our results
        if (($lookupRows === 1 && $lookupColumns > 1) || (!$hasResultVector && $lookupRows === 2 && $lookupColumns !== 2)) {
            $lookup_vector = LookupRef::TRANSPOSE($lookup_vector);
            $lookupRows = count($lookup_vector);
            $l = array_keys($lookup_vector);
            $lookupColumns = count($lookup_vector[array_shift($l)]);
        }

        if ($result_vector === null) {
            $result_vector = $lookup_vector;
        }
        $resultRows = count($result_vector);
        $l = array_keys($result_vector);
        $l = array_shift($l);
        $resultColumns = count($result_vector[$l]);
        // we correctly orient our results
        if ($resultRows === 1 && $resultColumns > 1) {
            $result_vector = LookupRef::TRANSPOSE($result_vector);
            $resultRows = count($result_vector);
            $r = array_keys($result_vector);
            $resultColumns = count($result_vector[array_shift($r)]);
        }

        if ($lookupRows === 2 && !$hasResultVector) {
            $result_vector = array_pop($lookup_vector);
            $lookup_vector = array_shift($lookup_vector);
        }

        if ($lookupColumns !== 2) {
            foreach ($lookup_vector as &$value) {
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
                $dataValue2 = array_shift($result_vector);
                if (is_array($dataValue2)) {
                    $dataValue2 = array_shift($dataValue2);
                }
                $value = [$key1 => $dataValue1, $key2 => $dataValue2];
            }
            unset($value);
        }

        return self::VLOOKUP($lookup_value, $lookup_vector, 2);
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
}
