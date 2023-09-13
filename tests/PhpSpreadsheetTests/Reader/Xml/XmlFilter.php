<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**  Define a Read Filter class implementing IReadFilter  */
class XmlFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row !== 4;
    }
}
