<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringHelperNoIconv extends StringHelper
{
    /**
     * Simulate that iconv is not available.
     */
    public static function getIsIconvEnabled(): bool
    {
        return false;
    }
}
