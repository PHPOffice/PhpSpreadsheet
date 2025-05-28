<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Vstack
{
    /**
     * Excel function VSTACK.
     *
     * @return mixed[]
     */
    public static function vstack(mixed ...$inputData): array|string
    {
        $returnMatrix = [];

        $columns = 0;
        foreach ($inputData as $matrix) {
            if (!is_array($matrix)) {
                $count = 1;
            } else {
                $count = count(reset($matrix)); //* @phpstan-ignore-line
            }
            $columns = max($columns, $count);
        }

        foreach ($inputData as $matrix) {
            if (!is_array($matrix)) {
                $matrix = [$matrix];
            }
            foreach ($matrix as $row) {
                if (!is_array($row)) {
                    $row = [$row];
                }
                $returnMatrix[] = array_values(array_pad($row, $columns, ExcelError::NA()));
            }
        }

        return $returnMatrix;
    }
}
