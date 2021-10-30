<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Matrix
{
    /**
     * TRANSPOSE.
     *
     * @param array|mixed $matrixData A matrix of values
     *
     * @return array
     */
    public static function transpose($matrixData)
    {
        $returnMatrix = [];
        if (!is_array($matrixData)) {
            $matrixData = [[$matrixData]];
        }

        $column = 0;
        foreach ($matrixData as $matrixRow) {
            $row = 0;
            foreach ($matrixRow as $matrixCell) {
                $returnMatrix[$row][$column] = $matrixCell;
                ++$row;
            }
            ++$column;
        }

        return $returnMatrix;
    }

    /**
     * INDEX.
     *
     * Uses an index to choose a value from a reference or array
     *
     * Excel Function:
     *        =INDEX(range_array, row_num, [column_num], [area_num])
     *
     * @param mixed $matrix A range of cells or an array constant
     * @param mixed $rowNum The row in the array or range from which to return a value.
     *                          If row_num is omitted, column_num is required.
     * @param mixed $columnNum The column in the array or range from which to return a value.
     *                          If column_num is omitted, row_num is required.
     *
     * TODO Provide support for area_num, currently not supported
     *
     * @return mixed the value of a specified cell or array of cells
     */
    public static function index($matrix, $rowNum = 0, $columnNum = 0)
    {
        $rowNum = ($rowNum === null) ? 0 : Functions::flattenSingleValue($rowNum);
        $columnNum = ($columnNum === null) ? 0 : Functions::flattenSingleValue($columnNum);

        try {
            $rowNum = LookupRefValidations::validatePositiveInt($rowNum);
            $columnNum = LookupRefValidations::validatePositiveInt($columnNum);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (!is_array($matrix) || ($rowNum > count($matrix))) {
            return Functions::REF();
        }

        $rowKeys = array_keys($matrix);
        $columnKeys = @array_keys($matrix[$rowKeys[0]]);

        if ($columnNum > count($columnKeys)) {
            return Functions::REF();
        }

        if ($columnNum === 0) {
            return self::extractRowValue($matrix, $rowKeys, $rowNum);
        }

        $columnNum = $columnKeys[--$columnNum];
        if ($rowNum === 0) {
            return array_map(
                function ($value) {
                    return [$value];
                },
                array_column($matrix, $columnNum)
            );
        }
        $rowNum = $rowKeys[--$rowNum];

        return $matrix[$rowNum][$columnNum];
    }

    private static function extractRowValue(array $matrix, array $rowKeys, int $rowNum)
    {
        if ($rowNum === 0) {
            return $matrix;
        }

        $rowNum = $rowKeys[--$rowNum];
        $row = $matrix[$rowNum];
        if (is_array($row)) {
            return [$rowNum => $row];
        }

        return $row;
    }
}
