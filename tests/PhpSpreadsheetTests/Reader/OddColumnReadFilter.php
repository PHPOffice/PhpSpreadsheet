<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Show only cells from odd columns.
 */
class OddColumnReadFilter implements IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        return (\ord(\substr($column, -1, 1)) % 2) === 1;
    }
}
