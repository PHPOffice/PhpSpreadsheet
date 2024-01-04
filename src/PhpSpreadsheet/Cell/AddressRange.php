<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

interface AddressRange
{
    public const MAX_ROW = 1048576;

    public const MAX_COLUMN = 'XFD';

    public const MAX_COLUMN_INT = 16384;

    public function from(): mixed;

    public function to(): mixed;

    public function __toString(): string;
}
