<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class Issue4416Filter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        $allowedColumns = ['A', 'B', 'C', 'D'];
        $allowedRows = range(1, 5);

        return in_array($columnAddress, $allowedColumns, true) && in_array($row, $allowedRows, true);
    }
}
