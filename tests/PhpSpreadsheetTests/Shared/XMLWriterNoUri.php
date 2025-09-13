<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

class XMLWriterNoUri extends \PhpOffice\PhpSpreadsheet\Shared\XMLWriter
{
    public function openUri(string $uri): bool
    {
        return false;
    }
}
