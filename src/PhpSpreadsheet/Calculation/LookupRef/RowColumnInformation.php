<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RowColumnInformation
{
    /**
     * Test if cellAddress is null or whitespace string.
     *
     * @param null|array|string $cellAddress A reference to a range of cells
     */
    private static function cellAddressNullOrWhitespace($cellAddress): bool
    {
        return $cellAddress === null || (!is_array($cellAddress) && trim($cellAddress) === '');
    }

    private static function cellColumn(?Cell $pCell): int
    {
        return ($pCell !== null) ? (int) Coordinate::columnIndexFromString($pCell->getColumn()) : 1;
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
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the column numbers
     *
     * @return int|int[]
     */
    public static function COLUMN($cellAddress = null, ?Cell $pCell = null)
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return self::cellColumn($pCell);
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $value) {
                $columnKey = preg_replace('/[^a-z]/i', '', $columnKey);

                return (int) Coordinate::columnIndexFromString($columnKey);
            }

            return self::cellColumn($pCell);
        }

        $cellAddress = $cellAddress ?? '';
        if ($pCell != null) {
            [,, $sheetName] = Helpers::extractWorksheet($cellAddress, $pCell);
            [,, $cellAddress] = Helpers::extractCellAddresses($cellAddress, true, $pCell->getWorksheet(), $sheetName);
        }
        [, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
        if (strpos($cellAddress, ':') !== false) {
            [$startAddress, $endAddress] = explode(':', $cellAddress);
            $startAddress = preg_replace('/[^a-z]/i', '', $startAddress);
            $endAddress = preg_replace('/[^a-z]/i', '', $endAddress);

            return range(
                (int) Coordinate::columnIndexFromString($startAddress),
                (int) Coordinate::columnIndexFromString($endAddress)
            );
        }

        $cellAddress = preg_replace('/[^a-z]/i', '', $cellAddress);

        return (int) Coordinate::columnIndexFromString($cellAddress);
    }

    /**
     * COLUMNS.
     *
     * Returns the number of columns in an array or reference.
     *
     * Excel Function:
     *        =COLUMNS(cellAddress)
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells
     *                                          for which you want the number of columns
     *
     * @return int|string The number of columns in cellAddress, or a string if arguments are invalid
     */
    public static function COLUMNS($cellAddress = null)
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return 1;
        }
        if (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        [$columns, $rows] = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $rows;
        }

        return $columns;
    }

    private static function cellRow(?Cell $pCell): int
    {
        return ($pCell !== null) ? $pCell->getRow() : 1;
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
     * @param null|array|string $cellAddress A reference to a range of cells for which you want the row numbers
     *
     * @return int|mixed[]|string
     */
    public static function ROW($cellAddress = null, ?Cell $pCell = null)
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return self::cellRow($pCell);
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $rowKey => $rowValue) {
                foreach ($rowValue as $columnKey => $cellValue) {
                    return (int) preg_replace('/\D/', '', $rowKey);
                }
            }

            return self::cellRow($pCell);
        }

        $cellAddress = $cellAddress ?? '';
        if ($pCell !== null) {
            [,, $sheetName] = Helpers::extractWorksheet($cellAddress, $pCell);
            [,, $cellAddress] = Helpers::extractCellAddresses($cellAddress, true, $pCell->getWorksheet(), $sheetName);
        }
        [, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
        if (strpos($cellAddress, ':') !== false) {
            [$startAddress, $endAddress] = explode(':', $cellAddress);
            $startAddress = preg_replace('/\D/', '', $startAddress);
            $endAddress = preg_replace('/\D/', '', $endAddress);

            return array_map(
                function ($value) {
                    return [$value];
                },
                range($startAddress, $endAddress)
            );
        }
        [$cellAddress] = explode(':', $cellAddress);

        return (int) preg_replace('/\D/', '', $cellAddress);
    }

    /**
     * ROWS.
     *
     * Returns the number of rows in an array or reference.
     *
     * Excel Function:
     *        =ROWS(cellAddress)
     *
     * @param null|array|string $cellAddress An array or array formula, or a reference to a range of cells
     *                                          for which you want the number of rows
     *
     * @return int|string The number of rows in cellAddress, or a string if arguments are invalid
     */
    public static function ROWS($cellAddress = null)
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return 1;
        }
        if (!is_array($cellAddress)) {
            return Functions::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        [$columns, $rows] = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $columns;
        }

        return $rows;
    }
}
