<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Selection
{
    use ArrayEnabled;

    /**
     * CHOOSE.
     *
     * Uses lookup_value to return a value from the list of value arguments.
     * Use CHOOSE to select one of up to 254 values based on the lookup_value.
     *
     * Excel Function:
     *        =CHOOSE(index_num, value1, [value2], ...)
     *
     * @param mixed $chosenEntry The entry to select from the list (indexed from 1)
     * @param mixed ...$chooseArgs Data values
     *
     * @return mixed The selected value
     */
    public static function choose(mixed $chosenEntry, mixed ...$chooseArgs)
    {
        if (is_array($chosenEntry)) {
            return self::evaluateArrayArgumentsSubset([self::class, __FUNCTION__], 1, $chosenEntry, ...$chooseArgs);
        }

        $entryCount = count($chooseArgs) - 1;

        if (is_numeric($chosenEntry)) {
            --$chosenEntry;
        } else {
            return ExcelError::VALUE();
        }
        $chosenEntry = floor($chosenEntry);
        if (($chosenEntry < 0) || ($chosenEntry > $entryCount)) {
            return ExcelError::VALUE();
        }

        if (is_array($chooseArgs[$chosenEntry])) {
            return Functions::flattenArray($chooseArgs[$chosenEntry]);
        }

        return $chooseArgs[$chosenEntry];
    }
}
