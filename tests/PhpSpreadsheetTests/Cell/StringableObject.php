<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use Stringable;

class StringableObject implements Stringable
{
    public function __toString(): string
    {
        return 'abc';
    }
}
