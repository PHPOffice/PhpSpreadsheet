<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelArrayPseudoFunctions
{
    public static function single(string $cellReference, Cell $cell): array
    {
        $worksheet = $cell->getWorksheet();

        [$referenceWorksheetName, $referenceCellCoordinate] = Worksheet::extractSheetTitle($cellReference, true);

        $result = ($referenceWorksheetName === '')
            ? $worksheet->getCell($referenceCellCoordinate)->getCalculatedValue()
            : $worksheet->getParent()
                ->getSheetByName($referenceWorksheetName)
                ->getCell($referenceCellCoordinate)->getCalculatedValue();

        return [[$result]];
    }

    public static function anchorArray($cellReference, Cell $cell): array
    {
        $coordinate = $cell->getCoordinate();
        $worksheet = $cell->getWorksheet();
        $value = $cell->getValue();

        [$referenceWorksheetName, $referenceCellCoordinate] = Worksheet::extractSheetTitle($cellReference, true);

        $calcEngine = Calculation::getInstance($worksheet->getParent());
        $result = $calcEngine->calculateCellValue(
            ($referenceWorksheetName === '')
                    ? $worksheet->getCell($referenceCellCoordinate)
                    : $worksheet->getParent()
                        ->getSheetByName($referenceWorksheetName)
                        ->getCell($referenceCellCoordinate)
        );

        // Set the result
        $worksheet->fromArray(
            $result,
            null,
            $coordinate,
            true
        );

        [$col, $row] = Coordinate::indexesFromString($coordinate);
        $row += count($result) - 1;
        $col = Coordinate::stringFromColumnIndex($col + count($result[0]) - 1);
        $formulaAttributes = ['t' => 'array', 'ref' => "{$coordinate}:{$col}{$row}"];

        // fromArray() will reset the value for this cell with the calculation result
        //      as well as updating the spillage cells,
        //  so we need to restore this cell to its formula value, attributes, and datatype
        $cell = $worksheet->getCell($coordinate);
        $cell->setValueExplicit($value, DataType::TYPE_FORMULA);
        $cell->setFormulaAttributes($formulaAttributes);

        $cell->updateInCollection();

        return $result;
    }
}
