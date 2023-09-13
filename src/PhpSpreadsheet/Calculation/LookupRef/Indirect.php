<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Indirect
{
    /**
     * Determine whether cell address is in A1 (true) or R1C1 (false) format.
     *
     * @param mixed $a1fmt Expect bool Helpers::CELLADDRESS_USE_A1 or CELLADDRESS_USE_R1C1,
     *                      but can be provided as numeric which is cast to bool
     */
    private static function a1Format(mixed $a1fmt): bool
    {
        $a1fmt = Functions::flattenSingleValue($a1fmt);
        if ($a1fmt === null) {
            return Helpers::CELLADDRESS_USE_A1;
        }
        if (is_string($a1fmt)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (bool) $a1fmt;
    }

    /**
     * Convert cellAddress to string, verify not null string.
     *
     * @param array|string $cellAddress
     */
    private static function validateAddress($cellAddress): string
    {
        $cellAddress = Functions::flattenSingleValue($cellAddress);
        if (!is_string($cellAddress) || !$cellAddress) {
            throw new Exception(ExcelError::REF());
        }

        return $cellAddress;
    }

    /**
     * INDIRECT.
     *
     * Returns the reference specified by a text string.
     * References are immediately evaluated to display their contents.
     *
     * Excel Function:
     *        =INDIRECT(cellAddress, bool) where the bool argument is optional
     *
     * @param array|string $cellAddress $cellAddress The cell address of the current cell (containing this formula)
     * @param mixed $a1fmt Expect bool Helpers::CELLADDRESS_USE_A1 or CELLADDRESS_USE_R1C1,
     *                      but can be provided as numeric which is cast to bool
     * @param Cell $cell The current cell (containing this formula)
     *
     * @return array|string An array containing a cell or range of cells, or a string on error
     */
    public static function INDIRECT($cellAddress, mixed $a1fmt, Cell $cell): string|array
    {
        [$baseCol, $baseRow] = Coordinate::indexesFromString($cell->getCoordinate());

        try {
            $a1 = self::a1Format($a1fmt);
            $cellAddress = self::validateAddress($cellAddress);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        [$cellAddress, $worksheet, $sheetName] = Helpers::extractWorksheet($cellAddress, $cell);

        if (preg_match('/^' . Calculation::CALCULATION_REGEXP_COLUMNRANGE_RELATIVE . '$/miu', $cellAddress, $matches)) {
            $cellAddress = self::handleRowColumnRanges($worksheet, ...explode(':', $cellAddress));
        } elseif (preg_match('/^' . Calculation::CALCULATION_REGEXP_ROWRANGE_RELATIVE . '$/miu', $cellAddress, $matches)) {
            $cellAddress = self::handleRowColumnRanges($worksheet, ...explode(':', $cellAddress));
        }

        try {
            [$cellAddress1, $cellAddress2, $cellAddress] = Helpers::extractCellAddresses($cellAddress, $a1, $cell->getWorkSheet(), $sheetName, $baseRow, $baseCol);
        } catch (Exception) {
            return ExcelError::REF();
        }

        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/miu', $cellAddress1, $matches))
            || (($cellAddress2 !== null) && (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/miu', $cellAddress2, $matches)))
        ) {
            return ExcelError::REF();
        }

        return self::extractRequiredCells($worksheet, $cellAddress);
    }

    /**
     * Extract range values.
     *
     * @return array Array of values in range if range contains more than one element.
     *                  Otherwise, a single value is returned.
     */
    private static function extractRequiredCells(?Worksheet $worksheet, string $cellAddress): array
    {
        return Calculation::getInstance($worksheet !== null ? $worksheet->getParent() : null)
            ->extractCellRange($cellAddress, $worksheet, false);
    }

    private static function handleRowColumnRanges(?Worksheet $worksheet, string $start, string $end): string
    {
        // Being lazy, we're only checking a single row/column to get the max
        if (ctype_digit($start) && $start <= 1048576) {
            // Max 16,384 columns for Excel2007
            $endColRef = ($worksheet !== null) ? $worksheet->getHighestDataColumn((int) $start) : AddressRange::MAX_COLUMN;

            return "A{$start}:{$endColRef}{$end}";
        } elseif (ctype_alpha($start) && strlen($start) <= 3) {
            // Max 1,048,576 rows for Excel2007
            $endRowRef = ($worksheet !== null) ? $worksheet->getHighestDataRow($start) : AddressRange::MAX_ROW;

            return "{$start}1:{$end}{$endRowRef}";
        }

        return "{$start}:{$end}";
    }
}
