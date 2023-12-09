<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

interface IReadFilter
{
    /**
     * Should this cell be read?
     *
     * @param string $columnAddress Column address (as a string value like "A", or "IV")
     * @param int $row Row number
     * @param string $worksheetName Optional worksheet name
     */
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool;
}
