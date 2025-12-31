<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringHelperNoIconv3 extends StringHelper
{
    protected static ?bool $isIconvEnabled = null;

    protected static bool $iconvTest3 = true;
}
