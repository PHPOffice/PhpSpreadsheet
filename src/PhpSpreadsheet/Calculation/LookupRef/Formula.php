<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class Formula
{
    /**
     * FORMULATEXT.
     *
     * @param mixed $cellReference The cell to check
     * @param Cell $cell The current cell (containing this formula)
     *
     * @return string
     */
    public static function text($cellReference = '', ?Cell $cell = null)
    {
        if ($cell === null) {
            return ExcelError::REF();
        }

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = trim($matches[3], "'");
        $worksheet = (!empty($worksheetName))
            ? $cell->getWorksheet()->getParentOrThrow()->getSheetByName($worksheetName)
            : $cell->getWorksheet();

        if (
            $worksheet === null ||
            !$worksheet->cellExists($cellReference) ||
            !$worksheet->getCell($cellReference)->isFormula()
        ) {
            return ExcelError::NA();
        }

        return $worksheet->getCell($cellReference)->getValue();
    }
}
