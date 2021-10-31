<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Show only cells from odd columns.
 */
class OddColumnReadFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        return (\ord(\substr($columnAddress, -1, 1)) % 2) === 1;
    }
}
