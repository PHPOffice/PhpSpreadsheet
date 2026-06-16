<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringHelperNoIntl extends StringHelper
{
    protected static string $testClass = 'simulateIntlUnavilable';
}
