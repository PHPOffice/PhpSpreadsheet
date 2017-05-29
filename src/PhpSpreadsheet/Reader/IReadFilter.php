<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

interface IReadFilter
{
    /**
     * Should this cell be read?
     *
     * @param $column string Column address (as a string value like "A", or "IV")
     * @param $row int Row number
     * @param $worksheetName string Optional worksheet name
     *
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '');
}
