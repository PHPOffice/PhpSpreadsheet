<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**  Define a Read Filter class implementing IReadFilter  */
class GnumericFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row !== 4;
    }
}
