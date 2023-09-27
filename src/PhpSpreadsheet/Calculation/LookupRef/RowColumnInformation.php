<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
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

    private static function cellColumn(?Cell $cell): int
    {
        return ($cell !== null) ? Coordinate::columnIndexFromString($cell->getColumn()) : 1;
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
    public static function COLUMN($cellAddress = null, ?Cell $cell = null): int|array
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return self::cellColumn($cell);
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $value) {
                $columnKey = (string) preg_replace('/[^a-z]/i', '', $columnKey);

                return Coordinate::columnIndexFromString($columnKey);
            }

            return self::cellColumn($cell);
        }

        $cellAddress = $cellAddress ?? '';
        if ($cell != null) {
            [,, $sheetName] = Helpers::extractWorksheet($cellAddress, $cell);
            [,, $cellAddress] = Helpers::extractCellAddresses($cellAddress, true, $cell->getWorksheet(), $sheetName);
        }
        [, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
        $cellAddress ??= '';

        if (str_contains($cellAddress, ':')) {
            [$startAddress, $endAddress] = explode(':', $cellAddress);
            $startAddress = (string) preg_replace('/[^a-z]/i', '', $startAddress);
            $endAddress = (string) preg_replace('/[^a-z]/i', '', $endAddress);

            return range(
                Coordinate::columnIndexFromString($startAddress),
                Coordinate::columnIndexFromString($endAddress)
            );
        }

        $cellAddress = (string) preg_replace('/[^a-z]/i', '', $cellAddress);

        return Coordinate::columnIndexFromString($cellAddress);
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
            return ExcelError::VALUE();
        }

        reset($cellAddress);
        $isMatrix = (is_numeric(key($cellAddress)));
        [$columns, $rows] = Calculation::getMatrixDimensions($cellAddress);

        if ($isMatrix) {
            return $rows;
        }

        return $columns;
    }

    private static function cellRow(?Cell $cell): int
    {
        return ($cell !== null) ? $cell->getRow() : 1;
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
     * @return int|mixed[]
     */
    public static function ROW($cellAddress = null, ?Cell $cell = null): int|array
    {
        if (self::cellAddressNullOrWhitespace($cellAddress)) {
            return self::cellRow($cell);
        }

        if (is_array($cellAddress)) {
            foreach ($cellAddress as $rowKey => $rowValue) {
                foreach ($rowValue as $columnKey => $cellValue) {
                    return (int) preg_replace('/\D/', '', $rowKey);
                }
            }

            return self::cellRow($cell);
        }

        $cellAddress = $cellAddress ?? '';
        if ($cell !== null) {
            [,, $sheetName] = Helpers::extractWorksheet($cellAddress, $cell);
            [,, $cellAddress] = Helpers::extractCellAddresses($cellAddress, true, $cell->getWorksheet(), $sheetName);
        }
        [, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
        $cellAddress ??= '';
        if (str_contains($cellAddress, ':')) {
            [$startAddress, $endAddress] = explode(':', $cellAddress);
            $startAddress = (int) (string) preg_replace('/\D/', '', $startAddress);
            $endAddress = (int) (string) preg_replace('/\D/', '', $endAddress);

            return array_map(
                fn ($value): array => [$value],
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
            return ExcelError::VALUE();
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
