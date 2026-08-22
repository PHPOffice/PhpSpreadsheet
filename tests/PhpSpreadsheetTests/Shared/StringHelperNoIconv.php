<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringHelperNoIconv extends StringHelper
{
    protected static ?bool $isIconvEnabled = null;

    protected static string $iconvName = 'simulateIconvUnavilable';
}
