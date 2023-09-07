<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testNAME(): void
    {
        $result = ExcelError::NAME();
        self::assertEquals('#NAME?', $result);
    }
}
