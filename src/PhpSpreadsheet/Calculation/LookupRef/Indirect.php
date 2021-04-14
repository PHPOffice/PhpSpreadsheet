<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Indirect
{
    /**
     * Determine whether cell address is in A1 (true) or R1C1 (false) format.
     *
     * @param null|bool|float|int|string $a1fmt
     */
    private static function a1Format($a1fmt): bool
    {
        $a1fmt = Functions::flattenSingleValue($a1fmt);
        if ($a1fmt === null) {
            return true;
        }
        if (is_string($a1fmt)) {
            throw new Exception(Functions::VALUE());
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
            throw new Exception(Functions::REF());
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
     * @param null|bool|float|int|string $a1fmt
     * @param Cell $pCell The current cell (containing this formula)
     *
     * @return array|string An array containing a cell or range of cells, or a string on error
     */
    public static function INDIRECT($cellAddress, $a1fmt, Cell $pCell)
    {
        try {
            $a1 = self::a1Format($a1fmt);
            $spreadsheet = $pCell->getWorksheet()->getParent();
            $cellAddress = self::validateAddress($cellAddress);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        [$cellAddress, $pSheet] = self::extractWorksheet($cellAddress, $pCell);

        [$cellAddress1, $cellAddress2, $cellAddress] = Helpers::extractCellAddresses($cellAddress, $a1, $spreadsheet, $pCell->getWorkSheet());

        if (
            (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress1, $matches)) ||
            (($cellAddress2 !== null) && (!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress2, $matches)))
        ) {
            return Functions::REF();
        }

        return self::extractRequiredCells($pSheet, $cellAddress);
    }

    private static function extractRequiredCells(?Worksheet $pSheet, string $cellAddress)
    {
        return Calculation::getInstance($pSheet !== null ? $pSheet->getParent() : null)
            ->extractCellRange($cellAddress, $pSheet, false);
    }

    private static function extractWorksheet($cellAddress, Cell $pCell): array
    {
        $sheetName = '';
        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }

        $pSheet = ($sheetName !== '')
            ? $pCell->getWorksheet()->getParent()->getSheetByName($sheetName)
            : $pCell->getWorksheet();

        return [$cellAddress, $pSheet];
    }
}
