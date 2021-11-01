<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**  Define a Read Filter class implementing IReadFilter  */
class GnumericFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        return $row !== 4;
    }
}
