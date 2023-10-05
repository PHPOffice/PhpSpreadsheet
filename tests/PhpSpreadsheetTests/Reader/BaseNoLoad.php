<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\BaseReader;

class BaseNoLoad extends BaseReader
{
    /**
     * @param resource|string $file
     */
    public function canRead($file): bool
    {
        return is_string($file) && $file !== '';
    }
}
