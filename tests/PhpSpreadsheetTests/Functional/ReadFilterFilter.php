<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilterFilter implements IReadFilter
{
    /**
     * @param string $column Column address (as a string value like "A", or "IV")
     * @param int $row Row number
     * @param string $worksheetName Optional worksheet name
     *
     * @return bool
     *
     * @see \PhpOffice\PhpSpreadsheet\Reader\IReadFilter::readCell()
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        // define filter range
        $rowMin = 2;
        $rowMax = 6;
        $columnMin = 'B';
        $columnMax = 'D';

        $r = $row;
        if ($r > $rowMax || $r < $rowMin) {
            return false;
        }

        $col = sprintf('%04s', $column);
        if (
            $col > sprintf('%04s', $columnMax) ||
            $col < sprintf('%04s', $columnMin)
        ) {
            return false;
        }

        return true;
    }
}
