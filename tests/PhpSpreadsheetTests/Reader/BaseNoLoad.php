<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\BaseReader;

class BaseNoLoad extends BaseReader
{
    public function canRead(string $filename): bool
    {
        return $filename !== '';
    }
}
