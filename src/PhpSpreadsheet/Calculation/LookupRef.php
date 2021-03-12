<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\VLookup;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
     * @Deprecated 1.18.0
     *
     * @see Use the cell() method in the LookupRef\Address class instead
     *
     * @param mixed $row Row number to use in the cell reference
     * @param mixed $column Column number to use in the cell reference
     * @param int $relativity Flag indicating the type of reference to return
     *                                1 or omitted    Absolute
     *                                2               Absolute row; relative column
     *                                3               Relative row; absolute column
     *                                4               Relative
     * @param bool $referenceStyle A logical value that specifies the A1 or R1C1 reference style.
     *                                TRUE or omitted      CELL_ADDRESS returns an A1-style reference
     *                                FALSE                CELL_ADDRESS returns an R1C1-style reference
     * @param string $sheetText Optional Name of worksheet to use
     *
     * @return string
     */
    public static function cellAddress($row, $column, $relativity = 1, $referenceStyle = true, $sheetText = '')
    {
        return Address::cell($row, $column, $relativity, $referenceStyle, $sheetText);
    }

    /**
     * COLUMN.
     *
     * Returns the column number of the given cell reference
     *     If the cell reference is a range of cells, COLUMN returns the column numbers of each column
     *        in the reference as a horizontal array.
     *     If cell reference is omitted, and the function is being called through the calculation engine,
     *        then it is assumed to be the reference of the cell in which the COLUMN function appears;
     *        otherwise this function returns 1.
     *
     * Excel Function:
     *        =COLUMN([cellAddress])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COLUMN() method in the LookupRef\RowColumnInformation class instead
     *
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the column numbers
     *
     * @return int|int[]
     */
    public static function COLUMN($cellAddress = null, ?Cell $cell = null)
    {
        return RowColumnInformation::COLUMN($cellAddress, $cell);
    }

    /**
     * COLUMNS.
     *
     * Returns the number of columns in an array or reference.
     *
     * Excel Function:
     *        =COLUMNS(cellAddress)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the COLUMNS() method in the LookupRef\RowColumnInformation class instead
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells
     *                                          for which you want the number of columns
     *
     * @return int|string The number of columns in cellAddress, or a string if arguments are invalid
     */
    public static function COLUMNS($cellAddress = null)
    {
        return RowColumnInformation::COLUMNS($cellAddress);
    }

    /**
     * ROW.
     *
     * Returns the row number of the given cell reference
     *     If the cell reference is a range of cells, ROW returns the row numbers of each row in the reference
     *        as a vertical array.
     *     If cell reference is omitted, and the function is being called through the calculation engine,
     *        then it is assumed to be the reference of the cell in which the ROW function appears;
     *        otherwise this function returns 1.
     *
     * Excel Function:
     *        =ROW([cellAddress])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the ROW() method in the LookupRef\RowColumnInformation class instead
     *
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the row numbers
     *
     * @return int|mixed[]|string
     */
    public static function ROW($cellAddress = null, ?Cell $cell = null)
    {
        return RowColumnInformation::ROW($cellAddress, $cell);
    }

    /**
     * ROWS.
     *
     * Returns the number of rows in an array or reference.
     *
     * Excel Function:
     *        =ROWS(cellAddress)
     *
     * @Deprecated 1.18.0
     *
     * @see Use the ROWS() method in the LookupRef\RowColumnInformation class instead
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells
     *                                          for which you want the number of rows
     *
     * @return int|string The number of rows in cellAddress, or a string if arguments are invalid
     */
    public static function ROWS($cellAddress = null)
    {
        return RowColumnInformation::ROWS($cellAddress);
    }

    /**
     * HYPERLINK.
     *
     * Excel Function:
     *        =HYPERLINK(linkURL,displayName)
     *
     * @param string $linkURL Value to check, is also the value returned when no error
     * @param string $displayName Value to return when testValue is an error condition
     * @param Cell $pCell The cell to set the hyperlink in
     *
     * @return mixed The value of $displayName (or $linkURL if $displayName was blank)
     */
    public static function HYPERLINK($linkURL = '', $displayName = null, ?Cell $pCell = null)
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
     * @TODO    Support for the optional a1 parameter introduced in Excel 2010
     */
    public static function INDIRECT($cellAddress = null, ?Cell $pCell = null)
    {
        $cellAddress = Functions::flattenSingleValue($cellAddress);
        if ($cellAddress === null || $cellAddress === '') {
            return Functions::REF();
        }

        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        if (strpos($cellAddress, ':') !== false) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }

        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress1, $matches)) ||
            (($cellAddress2 !== null) && (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress2, $matches)))
        ) {
            if (!preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/i', $cellAddress1, $matches)) {
                return Functions::REF();
            }

            if (strpos($cellAddress, '!') !== false) {
                [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
                $sheetName = trim($sheetName, "'");
                $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
            } else {
                $pSheet = $pCell->getWorksheet();
            }

            return Calculation::getInstance()->extractNamedRange($cellAddress, $pSheet, false);
        }

        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
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
     *
     * @return string A reference to a cell or range of cells
     */
    public static function OFFSET($cellAddress = null, $rows = 0, $columns = 0, $height = null, $width = null, ?Cell $pCell = null)
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
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }
        if (strpos($cellAddress, ':')) {
            [$startCell, $endCell] = explode(':', $cellAddress);
        } else {
            $startCell = $endCell = $cellAddress;
        }
        [$startCellColumn, $startCellRow] = Coordinate::coordinateFromString($startCell);
        [$endCellColumn, $endCellRow] = Coordinate::coordinateFromString($endCell);

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
     * @param mixed $matchType The number -1, 0, or 1. -1 means above, 0 means exact match, 1 means below.
     *                         If match_type is 1 or -1, the list has to be ordered.
     *
     * @return int|string The relative position of the found item
     */
    public static function MATCH($lookupValue, $lookupArray, $matchType = 1)
    {
        return LookupRef\ExcelMatch::MATCH($lookupValue, $lookupArray, $matchType);
    }

    /**
     * INDEX.
     *
     * Uses an index to choose a value from a reference or array
     *
     * Excel Function:
     *        =INDEX(range_array, row_num, [column_num])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the index() method in the LookupRef\Matrix class instead
     *
     * @param mixed $rowNum The row in the array or range from which to return a value.
     *                          If row_num is omitted, column_num is required.
     * @param mixed $columnNum The column in the array or range from which to return a value.
     *                             If column_num is omitted, row_num is required.
     * @param mixed $matrix
     *
     * @return mixed the value of a specified cell or array of cells
     */
    public static function INDEX($matrix, $rowNum = 0, $columnNum = 0)
    {
        return Matrix::index($matrix, $rowNum, $columnNum);
    }

    /**
     * TRANSPOSE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the transpose() method in the LookupRef\Matrix class instead
     *
     * @param array $matrixData A matrix of values
     *
     * @return array
     *
     * Unlike the Excel TRANSPOSE function, which will only work on a single row or column,
     *     this function will transpose a full matrix
     */
    public static function TRANSPOSE($matrixData)
    {
        return Matrix::transpose($matrixData);
    }

    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value
     *     in the same row based on the index_number.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the lookup() method in the LookupRef\VLookup class instead
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
        return VLookup::lookup($lookup_value, $lookup_array, $index_number, $not_exact_match);
    }

    /**
     * HLOOKUP
     * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value
     *     in the same column based on the index_number.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the lookup() method in the LookupRef\HLookup class instead
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
        return HLookup::lookup($lookup_value, $lookup_array, $index_number, $not_exact_match);
    }

    /**
     * LOOKUP
     * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the lookup() method in the LookupRef\Lookup class instead
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_vector The range of cells being searched
     * @param null|mixed $result_vector The column from which the matching value must be returned
     *
     * @return mixed The value of the found cell
     */
    public static function LOOKUP($lookup_value, $lookup_vector, $result_vector = null)
    {
        return Lookup::lookup($lookup_value, $lookup_vector, $result_vector);
    }

    /**
     * FORMULATEXT.
     *
     * @param mixed $cellReference The cell to check
     * @param Cell $pCell The current cell (containing this formula)
     *
     * @return string
     */
    public static function FORMULATEXT($cellReference = '', ?Cell $pCell = null)
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
