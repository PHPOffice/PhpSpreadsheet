<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Address
{
    use ArrayEnabled;

    public const ADDRESS_ABSOLUTE = 1;
    public const ADDRESS_COLUMN_RELATIVE = 2;
    public const ADDRESS_ROW_RELATIVE = 3;
    public const ADDRESS_RELATIVE = 4;

    public const REFERENCE_STYLE_A1 = true;
    public const REFERENCE_STYLE_R1C1 = false;

    /**
     * ADDRESS.
     *
     * Creates a cell address as text, given specified row and column numbers.
     *
     * Excel Function:
     *        =ADDRESS(row, column, [relativity], [referenceStyle], [sheetText])
     *
     * @param mixed $row Row number (integer) to use in the cell reference
     *                      Or can be an array of values
     * @param mixed $column Column number (integer) to use in the cell reference
     *                      Or can be an array of values
     * @param mixed $relativity Integer flag indicating the type of reference to return
     *                             1 or omitted    Absolute
     *                             2               Absolute row; relative column
     *                             3               Relative row; absolute column
     *                             4               Relative
     *                      Or can be an array of values
     * @param mixed $referenceStyle A logical (boolean) value that specifies the A1 or R1C1 reference style.
     *                                TRUE or omitted    ADDRESS returns an A1-style reference
     *                                FALSE              ADDRESS returns an R1C1-style reference
     *                      Or can be an array of values
     * @param mixed $sheetName Optional Name of worksheet to use
     *                      Or can be an array of values
     *
     * @return array|string
     *         If an array of values is passed as the $testValue argument, then the returned result will also be
     *            an array with the same dimensions
     */
    public static function cell($row, $column, $relativity = 1, $referenceStyle = true, $sheetName = '')
    {
        if (
            is_array($row) || is_array($column) ||
            is_array($relativity) || is_array($referenceStyle) || is_array($sheetName)
        ) {
            return self::evaluateArrayArguments(
                [self::class, __FUNCTION__],
                $row,
                $column,
                $relativity,
                $referenceStyle,
                $sheetName
            );
        }

        $relativity = $relativity ?? 1;
        $referenceStyle = $referenceStyle ?? true;

        if (($row < 1) || ($column < 1)) {
            return ExcelError::VALUE();
        }

        $sheetName = self::sheetName($sheetName);

        if (is_int($referenceStyle)) {
            $referenceStyle = (bool) $referenceStyle;
        }
        if ((!is_bool($referenceStyle)) || $referenceStyle === self::REFERENCE_STYLE_A1) {
            return self::formatAsA1($row, $column, $relativity, $sheetName);
        }

        return self::formatAsR1C1($row, $column, $relativity, $sheetName);
    }

    private static function sheetName(string $sheetName)
    {
        if ($sheetName > '') {
            if (strpos($sheetName, ' ') !== false || strpos($sheetName, '[') !== false) {
                $sheetName = "'{$sheetName}'";
            }
            $sheetName .= '!';
        }

        return $sheetName;
    }

    private static function formatAsA1(int $row, int $column, int $relativity, string $sheetName): string
    {
        $rowRelative = $columnRelative = '$';
        if (($relativity == self::ADDRESS_COLUMN_RELATIVE) || ($relativity == self::ADDRESS_RELATIVE)) {
            $columnRelative = '';
        }
        if (($relativity == self::ADDRESS_ROW_RELATIVE) || ($relativity == self::ADDRESS_RELATIVE)) {
            $rowRelative = '';
        }
        $column = Coordinate::stringFromColumnIndex($column);

        return "{$sheetName}{$columnRelative}{$column}{$rowRelative}{$row}";
    }

    private static function formatAsR1C1(int $row, int $column, int $relativity, string $sheetName): string
    {
        if (($relativity == self::ADDRESS_COLUMN_RELATIVE) || ($relativity == self::ADDRESS_RELATIVE)) {
            $column = "[{$column}]";
        }
        if (($relativity == self::ADDRESS_ROW_RELATIVE) || ($relativity == self::ADDRESS_RELATIVE)) {
            $row = "[{$row}]";
        }
        [$rowChar, $colChar] = AddressHelper::getRowAndColumnChars();

        return "{$sheetName}$rowChar{$row}$colChar{$column}";
    }
}
