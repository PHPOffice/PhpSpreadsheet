<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelArrayPseudoFunctions
{
    public static function single(string $cellReference, Cell $cell): mixed
    {
        $worksheet = $cell->getWorksheet();

        [$referenceWorksheetName, $referenceCellCoordinate] = Worksheet::extractSheetTitle($cellReference, true);
        if (preg_match('/^([$]?[a-z]{1,3})([$]?([0-9]{1,7})):([$]?[a-z]{1,3})([$]?([0-9]{1,7}))$/i', "$referenceCellCoordinate", $matches) === 1) {
            $ourRow = $cell->getRow();
            $firstRow = (int) $matches[3];
            $lastRow = (int) $matches[6];
            if ($ourRow < $firstRow || $ourRow > $lastRow || $matches[1] !== $matches[4]) {
                return ExcelError::VALUE();
            }
            $referenceCellCoordinate = $matches[1] . $ourRow;
        }
        $referenceCell = ($referenceWorksheetName === '')
            ? $worksheet->getCell((string) $referenceCellCoordinate)
            : $worksheet->getParentOrThrow()
                ->getSheetByNameOrThrow((string) $referenceWorksheetName)
                ->getCell((string) $referenceCellCoordinate);

        $result = $referenceCell->getCalculatedValue();
        while (is_array($result)) {
            $result = array_shift($result);
        }

        return $result;
    }

    public static function anchorArray(string $cellReference, Cell $cell): array|string
    {
        //$coordinate = $cell->getCoordinate();
        $worksheet = $cell->getWorksheet();

        [$referenceWorksheetName, $referenceCellCoordinate] = Worksheet::extractSheetTitle($cellReference, true);
        $referenceCell = ($referenceWorksheetName === '')
            ? $worksheet->getCell((string) $referenceCellCoordinate)
            : $worksheet->getParentOrThrow()
                ->getSheetByNameOrThrow((string) $referenceWorksheetName)
                ->getCell((string) $referenceCellCoordinate);

        // We should always use the sizing for the array formula range from the referenced cell formula
        //$referenceRange = null;
        /*if ($referenceCell->isFormula() && $referenceCell->isArrayFormula()) {
            $referenceRange = $referenceCell->arrayFormulaRange();
        }*/

        $calcEngine = Calculation::getInstance($worksheet->getParent());
        $result = $calcEngine->calculateCellValue($referenceCell, false);
        if (!is_array($result)) {
            $result = ExcelError::REF();
        }

        // Ensure that our array result dimensions match the specified array formula range dimensions,
        //    from the referenced cell, expanding or shrinking it as necessary.
        /*$result = Functions::resizeMatrix(
            $result,
            ...Coordinate::rangeDimension($referenceRange ?? $coordinate)
        );*/

        // Set the result for our target cell (with spillage)
        // But if we do write it, we get problems with #SPILL! Errors if the spreadsheet is saved
        // TODO How are we going to identify and handle a #SPILL! or a #CALC! error?
//        IOFactory::setLoading(true);
//        $worksheet->fromArray(
//            $result,
//            null,
//            $coordinate,
//            true
//        );
//        IOFactory::setLoading(true);

        // Calculate the array formula range that we should set for our target, based on our target cell coordinate
//        [$col, $row] = Coordinate::indexesFromString($coordinate);
//        $row += count($result) - 1;
//        $col = Coordinate::stringFromColumnIndex($col + count($result[0]) - 1);
//        $arrayFormulaRange = "{$coordinate}:{$col}{$row}";
//        $formulaAttributes = ['t' => 'array', 'ref' => $arrayFormulaRange];

        // Using fromArray() would reset the value for this cell with the calculation result
        //      as well as updating the spillage cells,
        //  so we need to restore this cell to its formula value, attributes, and datatype
//        $cell = $worksheet->getCell($coordinate);
//        $cell->setValueExplicit($value, DataType::TYPE_FORMULA, true, $arrayFormulaRange);
//        $cell->setFormulaAttributes($formulaAttributes);

//        $cell->updateInCollection();

        return $result;
    }
}
