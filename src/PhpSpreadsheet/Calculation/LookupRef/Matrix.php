<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

class Matrix
{
    /**
     * TRANSPOSE.
     *
     * @param array $matrixData A matrix of values
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
}
