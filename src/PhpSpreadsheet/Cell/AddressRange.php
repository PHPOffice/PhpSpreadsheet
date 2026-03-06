<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

/**
 * @template T
 */
interface AddressRange
{
    public const MAX_ROW = 1_048_576;
    public const MAX_COLUMN = 'XFD';
    public const MAX_COLUMN_INT = 16_384;

    public const MAX_ROW_XLS_OLD = 16_384;
    public const MAX_ROW_XLS = 65_536;
    public const MAX_COLUMN_XLS = 'IV';
    public const MAX_COLUMN_INT_XLS = 256;

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
