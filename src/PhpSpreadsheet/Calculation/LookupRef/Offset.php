<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Offset
{
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
    public static function OFFSET($cellAddress = null, $rows = 0, $columns = 0, $height = null, $width = null, ?Cell $cell = null)
    {
        $rows = Functions::flattenSingleValue($rows);
        $columns = Functions::flattenSingleValue($columns);
        $height = Functions::flattenSingleValue($height);
        $width = Functions::flattenSingleValue($width);

        if ($cellAddress === null || $cellAddress === '') {
            return ExcelError::VALUE();
        }

        if (!is_object($cell)) {
            return ExcelError::REF();
        }

        [$cellAddress, $worksheet] = self::extractWorksheet($cellAddress, $cell);

        $startCell = $endCell = $cellAddress;
        if (strpos($cellAddress, ':')) {
            [$startCell, $endCell] = explode(':', $cellAddress);
        }
        [$startCellColumn, $startCellRow] = Coordinate::coordinateFromString($startCell);
        [$endCellColumn, $endCellRow] = Coordinate::coordinateFromString($endCell);

        $startCellRow += $rows;
        $startCellColumn = Coordinate::columnIndexFromString($startCellColumn) - 1;
        $startCellColumn += $columns;

        if (($startCellRow <= 0) || ($startCellColumn < 0)) {
            return ExcelError::REF();
        }

        $endCellColumn = self::adjustEndCellColumnForWidth($endCellColumn, $width, $startCellColumn, $columns);
        $startCellColumn = Coordinate::stringFromColumnIndex($startCellColumn + 1);

        $endCellRow = self::adustEndCellRowForHeight($height, $startCellRow, $rows, $endCellRow);

        if (($endCellRow <= 0) || ($endCellColumn < 0)) {
            return ExcelError::REF();
        }
        $endCellColumn = Coordinate::stringFromColumnIndex($endCellColumn + 1);

        $cellAddress = "{$startCellColumn}{$startCellRow}";
        if (($startCellColumn != $endCellColumn) || ($startCellRow != $endCellRow)) {
            $cellAddress .= ":{$endCellColumn}{$endCellRow}";
        }

        return self::extractRequiredCells($worksheet, $cellAddress);
    }

    private static function extractRequiredCells(?Worksheet $worksheet, string $cellAddress)
    {
        return Calculation::getInstance($worksheet !== null ? $worksheet->getParent() : null)
            ->extractCellRange($cellAddress, $worksheet, false);
    }

    private static function extractWorksheet($cellAddress, Cell $cell): array
    {
        $cellAddress = self::assessCellAddress($cellAddress, $cell);

        $sheetName = '';
        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }

        $worksheet = ($sheetName !== '')
            ? $cell->getWorksheet()->getParent()->getSheetByName($sheetName)
            : $cell->getWorksheet();

        return [$cellAddress, $worksheet];
    }

    private static function assessCellAddress(string $cellAddress, Cell $cell): string
    {
        if (preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/mui', $cellAddress) !== false) {
            $cellAddress = Functions::expandDefinedName($cellAddress, $cell);
        }

        return $cellAddress;
    }

    private static function adjustEndCellColumnForWidth(string $endCellColumn, $width, int $startCellColumn, $columns)
    {
        $endCellColumn = Coordinate::columnIndexFromString($endCellColumn) - 1;
        if (($width !== null) && (!is_object($width))) {
            $endCellColumn = $startCellColumn + (int) $width - 1;
        } else {
            $endCellColumn += (int) $columns;
        }

        return $endCellColumn;
    }

    private static function adustEndCellRowForHeight($height, int $startCellRow, $rows, $endCellRow): int
    {
        if (($height !== null) && (!is_object($height))) {
            $endCellRow = $startCellRow + (int) $height - 1;
        } else {
            $endCellRow += (int) $rows;
        }

        return $endCellRow;
    }
}
