<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Indirect;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Offset;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\VLookup;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

/**
 * @deprecated 1.18.0
 */
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
     * @deprecated 1.18.0
     *      Use the cell() method in the LookupRef\Address class instead
     * @see LookupRef\Address::cell()
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
     * @param array|string $sheetText Optional Name of worksheet to use
     */
    public static function cellAddress(mixed $row, mixed $column, $relativity = 1, $referenceStyle = true, $sheetText = ''): string|array
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
     * @deprecated 1.18.0
     *      Use the COLUMN() method in the LookupRef\RowColumnInformation class instead
     * @see LookupRef\RowColumnInformation::COLUMN()
     *
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the column numbers
     *
     * @return int|int[]|string
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
     * @deprecated 1.18.0
     *      Use the COLUMNS() method in the LookupRef\RowColumnInformation class instead
     * @see LookupRef\RowColumnInformation::COLUMNS()
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
     * @deprecated 1.18.0
     *      Use the ROW() method in the LookupRef\RowColumnInformation class instead
     * @see LookupRef\RowColumnInformation::ROW()
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
     * @deprecated 1.18.0
     *      Use the ROWS() method in the LookupRef\RowColumnInformation class instead
     * @see LookupRef\RowColumnInformation::ROWS()
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
     * @deprecated 1.18.0
     *      Use the set() method in the LookupRef\Hyperlink class instead
     * @see LookupRef\Hyperlink::set()
     *
     * @param mixed $linkURL Expect string. Value to check, is also the value returned when no error
     * @param mixed $displayName Expect string. Value to return when testValue is an error condition
     * @param Cell $cell The cell to set the hyperlink in
     *
     * @return string The value of $displayName (or $linkURL if $displayName was blank)
     */
    public static function HYPERLINK(mixed $linkURL = '', mixed $displayName = null, ?Cell $cell = null)
    {
        return LookupRef\Hyperlink::set($linkURL, $displayName, $cell);
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
     * @deprecated 1.18.0
     *      Use the INDIRECT() method in the LookupRef\Indirect class instead
     * @see LookupRef\Indirect::INDIRECT()
     *
     * @param array|string $cellAddress $cellAddress The cell address of the current cell (containing this formula)
     * @param Cell $cell The current cell (containing this formula)
     *
     * @return array|string An array containing a cell or range of cells, or a string on error
     *
     * NOTE - INDIRECT() does not yet support the optional a1 parameter introduced in Excel 2010
     */
    public static function INDIRECT($cellAddress, Cell $cell)
    {
        return Indirect::INDIRECT($cellAddress, true, $cell);
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
     * @deprecated 1.18.0
     *      Use the OFFSET() method in the LookupRef\Offset class instead
     * @see LookupRef\Offset::OFFSET()
     *
     * @param null|string $cellAddress The reference from which you want to base the offset.
     *                                     Reference must refer to a cell or range of adjacent cells;
     *                                     otherwise, OFFSET returns the #VALUE! error value.
     * @param mixed $rows The number of rows, up or down, that you want the upper-left cell to refer to.
     *                        Using 5 as the rows argument specifies that the upper-left cell in the
     *                        reference is five rows below reference. Rows can be positive (which means
     *                        below the starting reference) or negative (which means above the starting
     *                        reference).
     * @param mixed $columns The number of columns, to the left or right, that you want the upper-left cell
     *                           of the result to refer to. Using 5 as the cols argument specifies that the
     *                           upper-left cell in the reference is five columns to the right of reference.
     *                           Cols can be positive (which means to the right of the starting reference)
     *                           or negative (which means to the left of the starting reference).
     * @param mixed $height The height, in number of rows, that you want the returned reference to be.
     *                          Height must be a positive number.
     * @param mixed $width The width, in number of columns, that you want the returned reference to be.
     *                         Width must be a positive number.
     *
     * @return array|int|string An array containing a cell or range of cells, or a string on error
     */
    public static function OFFSET($cellAddress = null, mixed $rows = 0, mixed $columns = 0, mixed $height = null, mixed $width = null, ?Cell $cell = null)
    {
        return Offset::OFFSET($cellAddress, $rows, $columns, $height, $width, $cell);
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
     * @deprecated 1.18.0
     *      Use the choose() method in the LookupRef\Selection class instead
     * @see LookupRef\Selection::choose()
     *
     * @param array $chooseArgs
     *
     * @return mixed The selected value
     */
    public static function CHOOSE(...$chooseArgs)
    {
        return LookupRef\Selection::choose(...$chooseArgs);
    }

    /**
     * MATCH.
     *
     * The MATCH function searches for a specified item in a range of cells
     *
     * Excel Function:
     *        =MATCH(lookup_value, lookup_array, [match_type])
     *
     * @deprecated 1.18.0
     *      Use the MATCH() method in the LookupRef\ExcelMatch class instead
     * @see LookupRef\ExcelMatch::MATCH()
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupArray The range of cells being searched
     * @param mixed $matchType The number -1, 0, or 1. -1 means above, 0 means exact match, 1 means below.
     *                         If match_type is 1 or -1, the list has to be ordered.
     *
     * @return array|float|int|string The relative position of the found item
     */
    public static function MATCH(mixed $lookupValue, mixed $lookupArray, mixed $matchType = 1): array|float|int|string
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
     * @deprecated 1.18.0
     *      Use the index() method in the LookupRef\Matrix class instead
     * @see LookupRef\Matrix::index()
     *
     * @param mixed $rowNum The row in the array or range from which to return a value.
     *                          If row_num is omitted, column_num is required.
     * @param mixed $columnNum The column in the array or range from which to return a value.
     *                             If column_num is omitted, row_num is required.
     *
     * @return mixed the value of a specified cell or array of cells
     */
    public static function INDEX(mixed $matrix, mixed $rowNum = 0, mixed $columnNum = 0)
    {
        return Matrix::index($matrix, $rowNum, $columnNum);
    }

    /**
     * TRANSPOSE.
     *
     * @deprecated 1.18.0
     *      Use the transpose() method in the LookupRef\Matrix class instead
     * @see LookupRef\Matrix::transpose()
     *
     * @param array $matrixData A matrix of values
     *
     * @return array
     *
     * Unlike the Excel TRANSPOSE function, which will only work on a single row or column,
     *     this function will transpose a full matrix
     */
    public static function TRANSPOSE($matrixData): array
    {
        return Matrix::transpose($matrixData);
    }

    /**
     * VLOOKUP
     * The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value
     *     in the same row based on the index_number.
     *
     * @deprecated 1.18.0
     *      Use the lookup() method in the LookupRef\VLookup class instead
     * @see LookupRef\VLookup::lookup()
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The column number in table_array from which the matching value must be returned.
     *                                The first column is 1.
     * @param mixed $not_exact_match determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function VLOOKUP(mixed $lookup_value, mixed $lookup_array, mixed $index_number, mixed $not_exact_match = true)
    {
        return VLookup::lookup($lookup_value, $lookup_array, $index_number, $not_exact_match);
    }

    /**
     * HLOOKUP
     * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value
     *     in the same column based on the index_number.
     *
     * @deprecated 1.18.0
     *      Use the lookup() method in the LookupRef\HLookup class instead
     * @see LookupRef\HLookup::lookup()
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_array The range of cells being searched
     * @param mixed $index_number The row number in table_array from which the matching value must be returned.
     *                                The first row is 1.
     * @param mixed $not_exact_match determines if you are looking for an exact match based on lookup_value
     *
     * @return mixed The value of the found cell
     */
    public static function HLOOKUP(mixed $lookup_value, mixed $lookup_array, mixed $index_number, mixed $not_exact_match = true)
    {
        return HLookup::lookup($lookup_value, $lookup_array, $index_number, $not_exact_match);
    }

    /**
     * LOOKUP
     * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
     *
     * @deprecated 1.18.0
     *      Use the lookup() method in the LookupRef\Lookup class instead
     * @see LookupRef\Lookup::lookup()
     *
     * @param mixed $lookup_value The value that you want to match in lookup_array
     * @param mixed $lookup_vector The range of cells being searched
     * @param null|mixed $result_vector The column from which the matching value must be returned
     *
     * @return mixed The value of the found cell
     */
    public static function LOOKUP(mixed $lookup_value, mixed $lookup_vector, $result_vector = null)
    {
        return Lookup::lookup($lookup_value, $lookup_vector, $result_vector);
    }

    /**
     * FORMULATEXT.
     *
     * @deprecated 1.18.0
     *      Use the text() method in the LookupRef\Formula class instead
     * @see LookupRef\Formula::text()
     *
     * @param mixed $cellReference The cell to check
     * @param Cell $cell The current cell (containing this formula)
     *
     * @return string
     */
    public static function FORMULATEXT(mixed $cellReference = '', ?Cell $cell = null)
    {
        return LookupRef\Formula::text($cellReference, $cell);
    }
}
