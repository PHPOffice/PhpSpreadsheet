<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**  Define a Read Filter class implementing IReadFilter  */
class XmlFilter implements IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        return $row !== 4;
    }
}
