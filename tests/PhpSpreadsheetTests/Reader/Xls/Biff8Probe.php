<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\Biff8;

class Biff8Probe extends Biff8
{
    /** @return array{value: string, size: int} */
    public static function readConstantArray(string $data): array
    {
        return parent::readBIFF8ConstantArray($data);
    }
}
