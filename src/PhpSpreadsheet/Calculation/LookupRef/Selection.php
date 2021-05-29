<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Selection
{
    /**
     * CHOOSE.
     *
     * Uses lookup_value to return a value from the list of value arguments.
     * Use CHOOSE to select one of up to 254 values based on the lookup_value.
     *
     * Excel Function:
     *        =CHOOSE(index_num, value1, [value2], ...)
     *
     * @param mixed ...$chooseArgs Data values
     *
     * @return mixed The selected value
     */
    public static function choose(...$chooseArgs)
    {
        $chosenEntry = Functions::flattenArray(array_shift($chooseArgs));
        $entryCount = count($chooseArgs) - 1;

        if (is_array($chosenEntry)) {
            $chosenEntry = array_shift($chosenEntry);
        }
        if (is_numeric($chosenEntry)) {
            --$chosenEntry;
        } else {
            return Functions::VALUE();
        }
        $chosenEntry = floor($chosenEntry);
        if (($chosenEntry < 0) || ($chosenEntry > $entryCount)) {
            return Functions::VALUE();
        }

        if (is_array($chooseArgs[$chosenEntry])) {
            return Functions::flattenArray($chooseArgs[$chosenEntry]);
        }

        return $chooseArgs[$chosenEntry];
    }
}
