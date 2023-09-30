<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

class StringableObject
{
    public function __toString(): string
    {
        return 'abc';
    }
}
