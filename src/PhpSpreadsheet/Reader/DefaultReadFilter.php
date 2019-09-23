<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

class DefaultReadFilter implements IReadFilter
{
    /**
     * Should this cell be read?
     *
     * @param string $column Column address (as a string value like "A", or "IV")
     * @param int $row Row number
     *
     * @return bool
     */
    public function readCell($column, $row)
    {
        return true;
    }
}
