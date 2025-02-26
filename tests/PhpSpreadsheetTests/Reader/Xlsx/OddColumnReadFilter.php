<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Show only cells from odd columns.
 */
class OddColumnReadFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return (\ord(\substr($columnAddress, -1, 1)) % 2) === 1;
    }
}
