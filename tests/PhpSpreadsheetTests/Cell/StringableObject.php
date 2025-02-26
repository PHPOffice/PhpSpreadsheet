<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

class StringableObject
{
    private int|string $value;

    public function __construct(int|string $value = 'abc')
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
