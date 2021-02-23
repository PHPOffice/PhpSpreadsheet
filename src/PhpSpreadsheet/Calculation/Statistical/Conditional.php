<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DCount;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Conditional
{
    private const PSEUDO_COLUMN_NAME = 'CONDITIONAL';

    /**
     * COUNTIF.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        COUNTIF(value1[,value2[, ...]],condition)
     *
     * @param mixed $arguments Data values
     * @param string $condition the criteria that defines which cells will be counted
     *
     * @return int
     */
    public static function COUNTIF($arguments, $condition)
    {
        // Filter out any empty values that shouldn't be included in a COUNT
        $arguments = array_filter(
            Functions::flattenArray($arguments),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        $arguments = array_merge([[self::PSEUDO_COLUMN_NAME]], array_chunk($arguments, 1));

        $condition = array_merge([[self::PSEUDO_COLUMN_NAME]], [[$condition]]);

        return DCount::evaluate($arguments, self::PSEUDO_COLUMN_NAME, $condition);
    }
}
