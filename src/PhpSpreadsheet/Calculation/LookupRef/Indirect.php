<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Indirect
{
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
     * @param null|Cell $pCell The current cell (containing this formula)
     *
     * @return array|string An array containing a cell or range of cells, or a string on error
     *
     * @TODO    Support for the optional a1 parameter introduced in Excel 2010
     */
    public static function INDIRECT($cellAddress = null, ?Cell $pCell = null)
    {
        $cellAddress = Functions::flattenSingleValue($cellAddress);
        if ($cellAddress === null || $cellAddress === '' || !is_object($pCell)) {
            return Functions::REF();
        }

        [$cellAddress, $pSheet] = self::extractWorksheet($cellAddress, $pCell);

        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        if (strpos($cellAddress, ':') !== false) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }

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
