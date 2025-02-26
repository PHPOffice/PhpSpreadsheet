<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**  Define a Read Filter class implementing IReadFilter  */
class CsvContiguousFilter implements IReadFilter
{
    private int $startRow = 0;

    private int $endRow = 0;

    private int $filterType = 0;

    /**
     * Set the list of rows that we want to read.
     */
    public function setRows(int $startRow, int $chunkSize): void
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function setFilterType(int $type): void
    {
        $this->filterType = $type;
    }

    public function filter1(int $row): bool
    {
        //  Include rows 1-10, followed by 100-110, etc.
        return $row % 100 <= 10;
    }

    public function filter0(int $row): bool
    {
        //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }

        return false;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        if ($this->filterType == 1) {
            return $this->filter1($row);
        }

        return $this->filter0($row);
    }
}
