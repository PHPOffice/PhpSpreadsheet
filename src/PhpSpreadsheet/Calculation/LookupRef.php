<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LookupRef
{
    /**
     * CELL_ADDRESS.
     *
     * Creates a cell address as text, given specified row and column numbers.
     *
     * Excel Function:
     *        =ADDRESS(row, column, [relativity], [referenceStyle], [sheetText])
     *
     * @param mixed $row Row number to use in the cell reference
     * @param mixed $column Column number to use in the cell reference
     * @param int $relativity Flag indicating the type of reference to return
     *                                1 or omitted    Absolute
     *                                2                Absolute row; relative column
     *                                3                Relative row; absolute column
     *                                4                Relative
     * @param bool $referenceStyle A logical value that specifies the A1 or R1C1 reference style.
     *                                TRUE or omitted        CELL_ADDRESS returns an A1-style reference
     *                                FALSE                CELL_ADDRESS returns an R1C1-style reference
     * @param string $sheetText Optional Name of worksheet to use
     *
     * @return string
     */
    public static function cellAddress($row, $column, $relativity = 1, $referenceStyle = true, $sheetText = '')
    {
        $row = Functions::flattenSingleValue($row);
        $column = Functions::flattenSingleValue($column);
        $relativity = Functions::flattenSingleValue($relativity);
        $sheetText = Functions::flattenSingleValue($sheetText);

        if (($row < 1) || ($column < 1)) {
            return Functions::VALUE();
        }

        if ($sheetText > '') {
            if (strpos($sheetText, ' ') !== false) {
                $sheetText = "'" . $sheetText . "'";
            }
            $sheetText .= '!';
        }
        if ((!is_bool($referenceStyle)) || $referenceStyle) {
            $rowRelative = $columnRelative = '$';
            $column = Coordinate::stringFromColumnIndex($column);
            if (($relativity == 2) || ($relativity == 4)) {
                $columnRelative = '';
            }
            if (($relativity == 3) || ($relativity == 4)) {
                $rowRelative = '';
            }

            return $sheetText . $columnRelative . $column . $rowRelative . $row;
        }
        if (($relativity == 2) || ($relativity == 4)) {
            $column = '[' . $column . ']';
        }
        if (($relativity == 3) || ($relativity == 4)) {
            $row = '[' . $row . ']';
        }

        return $sheetText . 'R' . $row . 'C' . $column;
    }

    /**
     * COLUMN.
     *
     * Returns the column number of the given cell reference
     * If the cell reference is a range of cells, COLUMN returns the column numbers of each column in the reference as a horizontal array.
     * If cell reference is omitted, and the function is being called through the calculation engine, then it is assumed to be the
     *        reference of the cell in which the COLUMN function appears; otherwise this function returns 0.
     *
     * Excel Function:
     *        =COLUMN([cellAddress])
     *
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the column numbers
     *
     * @return int|int[]
     */
    public static function COLUMN($cellAddress = null)
    {
        if ($cellAddress === null || trim($cellAddress) === '') {
            return 0;
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $value) {
                $columnKey = preg_replace('/[^a-z]/i', '', $columnKey);

                return (int) Coordinate::columnIndexFromString($columnKey);
            }
        } else {
            list($sheet, $cellAddress) = Worksheet::extractSheetTitle($cellAddress, true);
            if (strpos($cellAddress, ':') !== false) {
                list($startAddress, $endAddress) = explode(':', $cellAddress);
                $startAddress = preg_replace('/[^a-z]/i', '', $startAddress);
                $endAddress = preg_replace('/[^a-z]/i', '', $endAddress);
                $returnValue = [];
                do {
                    $returnValue[] = (int) Coordinate::columnIndexFromString($startAddress);
                } while ($startAddress++ != $endAddress);

                return $returnValue;
            }
            $cellAddress = preg_replace('/[^a-z]/i', '', $cellAddress);

            return (int) Coordinate::columnIndexFromString($cellAddress);
        }
    }

    /**
     * COLUMNS.
     *
     * Returns the number of columns in an array or reference.
     *
     * Excel Function:
     *        =COLUMNS(cellAddress)
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells for which you want the number of columns
     *
     * @return int The number of columns in cellAddress
     */
    public static function COLUMNS($cellAddress = null)
    {
        if ($cellAddress === null || $cellAddress === '') {
            return 1;
        } elseif (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        list($columns, $rows) = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $rows;
        }

        return $columns;
    }

    /**
     * ROW.
     *
     * Returns the row number of the given cell reference
     * If the cell reference is a range of cells, ROW returns the row numbers of each row in the reference as a vertical array.
     * If cell reference is omitted, and the function is being called through the calculation engine, then it is assumed to be the
     *        reference of the cell in which the ROW function appears; otherwise this function returns 0.
     *
     * Excel Function:
     *        =ROW([cellAddress])
     *
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the row numbers
     *
     * @return int or array of integer
     */
    public static function ROW($cellAddress = null)
    {
        if ($cellAddress === null || trim($cellAddress) === '') {
            return 0;
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $rowValue) {
                foreach ($rowValue as $rowKey => $cellValue) {
                    return (int) preg_replace('/\D/', '', $rowKey);
                }
            }
        } else {
            list($sheet, $cellAddress) = Worksheet::extractSheetTitle($cellAddress, true);
            if (strpos($cellAddress, ':') !== false) {
                list($startAddress, $endAddress) = explode(':', $cellAddress);
                $startAddress = preg_replace('/\D/', '', $startAddress);
                $endAddress = preg_replace('/\D/', '', $endAddress);
                $returnValue = [];
                do {
                    $returnValue[][] = (int) $startAddress;
                } while ($startAddress++ != $endAddress);

                return $returnValue;
            }
            list($cellAddress) = explode(':', $cellAddress);

            return (int) preg_replace('/\D/', '', $cellAddress);
        }
    }

    /**
     * ROWS.
     *
     * Returns the number of rows in an array or reference.
     *
     * Excel Function:
     *        =ROWS(cellAddress)
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells for which you want the number of rows
     *
     * @return int The number of rows in cellAddress
     */
    public static function ROWS($cellAddress = null)
    {
        if ($cellAddress === null || $cellAddress === '') {
            return 1;
        } elseif (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        list($columns, $rows) = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $columns;
        }

        return $rows;
    }

    /**
     * HYPERLINK.
     *
     * Excel Function:
     *        =HYPERLINK(linkURL,displayName)
     *
     * @category Logical Functions
     *
     * @param string $linkURL Value to check, is also the value returned when no error
     * @param string $displayName Value to return when testValue is an error condition
     * @param Cell $pCell The cell to set the hyperlink in
     *
     * @return mixed The value of $displayName (or $linkURL if $displayName was blank)
     */
    public static function HYPERLINK($linkURL = '', $displayName = null, Cell $pCell = null)
    {
        $linkURL = ($linkURL === null) ? '' : Functions::flattenSingleValue($linkURL);
        $displayName = ($displayName === null) ? '' : Functions::flattenSingleValue($displayName);

        if ((!is_object($pCell)) || (trim($linkURL) == '')) {
            return Functions::REF();
        }

        if ((is_object($displayName)) || trim($displayName) == '') {
            $displayName = $linkURL;
        }

        $pCell->getHyperlink()->setUrl($linkURL);
        $pCell->getHyperlink()->setTooltip($displayName);

        return $displayName;
    }

    /**
     * INDIRECT.
     *
     * Returns the reference specified by a text string.
     * References are immediately evaluated to display their contents.
     *
     * Excel Function:
     *        =INDIRECT(cellAddress)
     *
     * NOTE - INDIRECT() does not yet support the optional a1 parameter introduced in Excel 2010
     *
     * @param null|array|string $cellAddress $cellAddress The cell address of the current cell (containing this formula)
     * @param Cell $pCell The current cell (containing this formula)
     *
     * @return mixed The cells referenced by cellAddress
     *
     * @todo    Support for the optional a1 parameter introduced in Excel 2010
     */
    public static function INDIRECT($cellAddress = null, Cell $pCell = null)
    {
        $cellAddress = Functions::flattenSingleValue($cellAddress);
        if ($cellAddress === null || $cellAddress === '') {
            return Functions::REF();
        }

        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        if (strpos($cellAddress, ':') !== false) {
            list($cellAddress1, $cellAddress2) = explode(':', $cellAddress);
        }

        if ((!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress1, $matches)) ||
            (($cellAddress2 !== null) && (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress2, $matches)))) {
            if (!preg_match('/^' . Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $cellAddress1, $matches)) {
                return Functions::REF();
            }

            if (strpos($cellAddress, '!') !== false) {
                list($sheetName, $cellAddress) = Worksheet::extractSheetTitle($cellAddress, true);
                $sheetName = trim($sheetName, "'");
                $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
            } else {
                $pSheet = $pCell->getWorksheet();
            }

            return Calculation::getInstance()->extractNamedRange($cellAddress, $pSheet, false);
        }

        if (strpos($cellAddress, '!') !== false) {
            list($sheetName, $cellAddress) = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }

        return Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }

    /**
     * OFFSET.
     *
     * Returns a reference to a range that is a specified number of rows and columns from a cell or range of cells.
     * The reference that is returned can be a single cell or a range of cells. You can specify the number of rows and
     * the number of columns to be returned.
     *
     * Excel Function:
     *        =OFFSET(cellAddress, rows, cols, [height], [width])
     *
     * @param null|string $cellAddress The reference from which you want to base the offset. Reference must refer to a cell or
     *                                range of adjacent cells; otherwise, OFFSET returns the #VALUE! error value.
     * @param mixed $rows The number of rows, up or down, that you want the upper-left cell to refer to.
     *                                Using 5 as the rows argument specifies that the upper-left cell in the reference is
     *                                five rows below reference. Rows can be positive (which means below the starting reference)
     *                                or negative (which means above the starting reference).
     * @param mixed $columns The number of columns, to the left or right, that you want the upper-left cell of the result
     *                                to refer to. Using 5 as the cols argument specifies that the upper-left cell in the
     *                                reference is five columns to the right of reference. Cols can be positive (which means
     *                                to the right of the starting reference) or negative (which means to the left of the
     *                                starting reference).
     * @param mixed $height The height, in number of rows, that you want the returned reference to be. Height must be a positive number.
     * @param mixed $width The width, in number of columns, that you want the returned reference to be. Width must be a positive number.
     * @param null|Cell $pCell
     *
     * @return string A reference to a cell or range of cells
     */
    public static function OFFSET($cellAddress = null, $rows = 0, $columns = 0, $height = null, $width = null, Cell $pCell = null)
    {
        $rows = Functions::flattenSingleValue($rows);
        $columns = Functions::flattenSingleValue($columns);
        $height = Functions::flattenSingleValue($height);
        $width = Functions::flattenSingleValue($width);
        if ($cellAddress === null) {
            return 0;
        }

        if (!is_object($pCell)) {
            return Functions::REF();
        }

        $sheetName = null;
        if (strpos($cellAddress, '!')) {
            list($sheetName, $cellAddress) = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }
        if (strpos($cellAddress, ':')) {
            list($startCell, $endCell) = explode(':', $cellAddress);
        } else {
            $startCell = $endCell = $cellAddress;
        }
        list($startCellColumn, $startCellRow) = Coordinate::coordinateFromString($startCell);
        list($endCellColumn, $endCellRow) = Coordinate::coordinateFromString($endCell);

        $startCellRow += $rows;
        $startCellColumn = Coordinate::columnIndexFromString($startCellColumn) - 1;
        $startCellColumn += $columns;

        if (($startCellRow <= 0) || ($startCellColumn < 0)) {
            return Functions::REF();
        }
        $endCellColumn = Coordinate::columnIndexFromString($endCellColumn) - 1;
        if (($width != null) && (!is_object($width))) {
            $endCellColumn = $startCellColumn + $width - 1;
        } else {
            $endCellColumn += $columns;
        }
        $startCellColumn = Coordinate::stringFromColumnIndex($startCellColumn + 1);

        if (($height != null) && (!is_object($height))) {
            $endCellRow = $startCellRow + $height - 1;
        } else {
            $endCellRow += $rows;
        }

        if (($endCellRow <= 0) || ($endCellColumn < 0)) {
            return Functions::REF();
        }
        $endCellColumn = Coordinate::stringFromColumnIndex($endCellColumn + 1);

        $cellAddress = $startCellColumn . $startCellRow;
        if (($startCellColumn != $endCellColumn) || ($startCellRow != $endCellRow)) {
            $cellAddress .= ':' . $endCellColumn . $endCellRow;
        }

        if ($sheetName !== null) {
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }

        return Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }

    /**
     * CHOOSE.
     *
     * Uses lookup_value to return a value from the list of value arguments.
     * Use CHOOSE to select one of up to 254 values based on the lookup_value.
     *
     * Excel Function:
     *        =CHOOSE(index_num, value1, [value2], ...)
     *
     * @param mixed $index_num Specifies which value argument is selected.
     *                            Index_num must be a number between 1 and 254, or a formula or reference to a cell containing a number
     *                                between 1 and 254.
     * @param mixed $value1 ... Value1 is required, subsequent values are optional.
     *                            Between 1 to 254 value arguments from which CHOOSE selects a value or an action to perform based on
     *                                index_num. The arguments can be numbers, cell references, defined names, formulas, functions, or
     *                                text.
     *
     * @return mixed The selected value
     */
    public static function CHOOSE(...$chooseArgs)
    {
        $chosenEntry = Functions::flattenArray(array_shift($chooseArgs));
        $entryCount = count($chooseArgs) - 1;

        if (is_array($chosenEntry)) {
            $chosenEntry = array_shift($chosenEntry);
        }
        if ((is_numeric($chosenEntry)) && (!is_bool($chosenEntry))) {
            --$chosenEntry;
        } else {
            return Functions::VALUE();
        }
        $chosenEntry = floor($chosenEntry);
        if (($chosenEntry < 0) || ($chosenEntry > $entryCount)) {
            return Functions::VALUE();
        }

        if (is_array($chooseArgs[$chosenEntry])) {
            return Functions::flattenArray($chooseArgs[$chosenEntry]);
        }

        return $chooseArgs[$chosenEntry];
    }

    /**
     * MATCH.
     *
     * The MATCH function searches for a specified item in a range of cells
     *
     * Excel Function:
     *        =MATCH(lookup_value, lookup_array, [match_type])
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupArray The range of cells being searched
     * @param mixed $matchType The number -1, 0, or 1. -1 means above, 0 means exact match, 1 means below. If match_type is 1 or -1, the list has to be ordered.
     *
     * @return int The relative position of the found item
     */
    public static function MATCH($lookupValue, $lookupArray, $matchType = 1)
    {
        $lookupArray = Functions::flattenArray($lookupArray);
        $lookupValue = Functions::flattenSingleValue($lookupValue);
        $matchType = ($matchType === null) ? 1 : (int) Functions::flattenSingleValue($matchType);

        $initialLookupValue = $lookupValue;
        // MATCH is not case sensitive
        $lookupValue = StringHelper::strToLower($lookupValue);

        // Lookup_value type has to be number, text, or logical values
        if ((!is_numeric($lookupValue)) && (!is_string($lookupValue)) && (!is_bool($lookupValue))) {
            return Functions::NA();
        }

        // Match_type is 0, 1 or -1
        if (($matchType !== 0) && ($matchType !== -1) && ($matchType !== 1)) {
            return Functions::NA();
        }

        // Lookup_array should not be empty
        $lookupArraySize = count($lookupArray);
        if ($lookupArraySize <= 0) {
            return Functions::NA();
        }

        // Lookup_array should contain only number, text, or logical values, or empty (null) cells
        foreach ($lookupArray as $i => $lookupArrayValue) {
            //    check the type of the value
            if ((!is_numeric($lookupArrayValue)) && (!is_string($lookupArrayValue)) &&
                (!is_bool($lookupArrayValue)) && ($lookupArrayValue !== null)
            ) {
                return Functions::NA();
            }
            // Convert strings to lowercase for case-insensitive testing
            if (is_string($lookupArrayValue)) {
                $lookupArray[$i] = StringHelper::strToLower($lookupArrayValue);
            }
            if (($lookupArrayValue === null) && (($matchType == 1) || ($matchType == -1))) {
                $lookupArray = array_slice($lookupArray, 0, $i - 1);
            }
        }

        if ($matchType == 1) {
            // If match_type is 1 the list has to be processed from last to first

            $lookupArray = array_reverse($lookupArray);
            $keySet = array_reverse(array_keys($lookupArray));
        }

        // **
        // find the match
        // **

        if ($matchType == 0 || $matchType == 1) {
            foreach ($lookupArray as $i => $lookupArrayValue) {
                $onlyNumeric = is_numeric($lookupArrayValue) && is_numeric($lookupValue);
                $onlyNumericExactMatch = $onlyNumeric && $lookupArrayValue == $lookupValue;
                $nonOnlyNumericExactMatch = !$onlyNumeric && $lookupArrayValue === $lookupValue;
                $exactMatch = $onlyNumericExactMatch || $nonOnlyNumericExactMatch;
                if (($matchType == 0) && $exactMatch) {
                    //    exact match
                    return $i + 1;
                } elseif (($matchType == 1) && ($lookupArrayValue <= $lookupValue)) {
                    $i = array_search($i, $keySet);

                    // The current value is the (first) match
                    return $i + 1;
                }
            }
        } else {
            // matchType = -1

            // "Special" case: since the array it's supposed to be ordered in descending order, the
            // Excel algorithm gives up immediately if the first element is smaller than the searched value
            if ($lookupArray[0] < $lookupValue) {
                return Functions::NA();
            }

            $maxValueKey = null;

            // The basic algorithm is:
            // Iterate and keep the highest match until the next element is smaller than the searched value.
            // Return immediately if perfect match is found
            foreach ($lookupArray as $i => $lookupArrayValue) {
                if ($lookupArrayValue == $lookupValue) {
                    // Another "special" case. If a perfect match is found,
                    // the algorithm gives up immediately
                    return $i + 1;
                } elseif ($lookupArrayValue >= $lookupValue) {
                    $maxValueKey = $i + 1;
                }
            }

            if ($maxValueKey !== null) {
                return $maxValueKey;
            }
        }

        // Unsuccessful in finding a match, return #N/A error value
        return Functions::NA();
    }

    /**
     * INDEX.
     *
     * Uses an index to choose a value from a reference or array
     *
     * Excel Function:
     *        =INDEX(range_array, row_num, [column_num])
     *
     * @param mixed $arrayValues A range of cells or an array constant
     * @param mixed $rowNum The row in array from which to return a value. If row_num is omitted, column_num is required.
     * @param mixed $columnNum The column in array from which to return a value. If column_num is omitted, row_num is required.
     *
     * @return mixed the value of a specified cell or array of cells
     */
    public static function INDEX($arrayValues, $rowNum = 0, $columnNum = 0)
    {
        $rowNum = Functions::flattenSingleValue($rowNum);
        $columnNum = Functions::flattenSingleValue($columnNum);

        if (($rowNum < 0) || ($columnNum < 0)) {
            return Functions::VALUE();
        }

        if (!is_array($arrayValues) || ($rowNum > count($arrayValues))) {
            return Functions::REF();
        }

        $rowKeys = array_keys($arrayValues);
        $columnKeys = @array_keys($arrayValues[$rowKeys[0]]);

        if ($columnNum > count($columnKeys)) {
            return Functions::VALUE();
        } elseif ($columnNum == 0) {
            if ($rowNum == 0) {
                return $arrayValues;
            }
            $rowNum = $rowKeys[--$rowNum];
            $returnArray = [];
            foreach ($arrayValues as $arrayColumn) {
                if (is_array($arrayColumn)) {
                    if (isset($arrayColumn[$rowNum])) {
                        $returnArray[] = $arrayColumn[$rowNum];
                    } else {
                        return [$rowNum => $arrayValues[$rowNum]];
                    }
                } else {
                    return $arrayValues[$rowNum];
                }
            }

            return $returnArray;
        }
        $columnNum = $columnKeys[--$columnNum];
        if ($rowNum > count($rowKeys)) {
            return Functions::VALUE();
        } elseif ($rowNum == 0) {
            return $arrayValues[$columnNum];
        }
        $rowNum = $rowKeys[--$rowNum];

        return $arrayValues[$rowNum][$columnNum];
    }

    /**
     * TRANSPOSE.
     *
     * @param array $matrixData A matrix of values
     *
     * @return array
     *
     * Unlike the Excel TRANSPOSE function, which will only work on a single row or column, this function will transpose a full matrix
     */
    public static function TRANSPOSE($matrixData)
    {
        $returnMatrix = [];
        if (!is_array($matrixData)) {
            $matrixData = [[$matrixData]];
        }

        $column = 0;
        foreach ($matrixData as $matrixRow) {
            $row = 0;
            foreach ($matrixRow as $matrixCell) {
                $returnMatrix[$row][$column] = $matrixCell;
                ++$row;
            }
            ++$column;
        }

        return $returnMatrix;
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

    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value in the same row based on the index_number.
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The column number in table_array from which the matching value must be returned. The first column is 1.
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
            if ((is_numeric($lookup_value) && is_numeric($rowData[$firstColumn]) && ($rowData[$firstColumn] > $lookup_value)) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]) && ($firstLower > $lookupLower))) {
                break;
            }
            // remember the last key, but only if datatypes match
            if ((is_numeric($lookup_value) && is_numeric($rowData[$firstColumn])) ||
                (!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]))) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif (($firstLower == $lookupLower)
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
     * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value in the same column based on the index_number.
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The row number in table_array from which the matching value must be returned. The first row is 1.
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
        $firstRow = array_pop($f);
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

            if (($bothNumeric && $rowData > $lookup_value) ||
                ($bothNotNumeric && $rowDataLower > $lookupLower)) {
                break;
            }

            // Remember the last key, but only if datatypes match (as in VLOOKUP)
            if ($bothNumeric || $bothNotNumeric) {
                if ($not_exact_match) {
                    $rowNumber = $rowKey;

                    continue;
                } elseif ($rowDataLower === $lookupLower
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
            $lookup_vector = self::TRANSPOSE($lookup_vector);
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
            $result_vector = self::TRANSPOSE($result_vector);
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

    /**
     * FORMULATEXT.
     *
     * @param mixed $cellReference The cell to check
     * @param Cell $pCell The current cell (containing this formula)
     *
     * @return string
     */
    public static function FORMULATEXT($cellReference = '', Cell $pCell = null)
    {
        if ($pCell === null) {
            return Functions::REF();
        }

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = trim($matches[3], "'");
        $worksheet = (!empty($worksheetName))
            ? $pCell->getWorksheet()->getParent()->getSheetByName($worksheetName)
            : $pCell->getWorksheet();

        if (!$worksheet->getCell($cellReference)->isFormula()) {
            return Functions::NA();
        }

        return $worksheet->getCell($cellReference)->getValue();
    }
}
