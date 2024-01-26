<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

/**
 * @template T
 */
interface AddressRange
{
    public const MAX_ROW = 1048576;

    public const MAX_COLUMN = 'XFD';

    public const MAX_COLUMN_INT = 16384;

    /**
     * @return T
     */
    public function from(): mixed;

    /**
     * @return T
     */
    public function to(): mixed;

    public function __toString(): string;
}
