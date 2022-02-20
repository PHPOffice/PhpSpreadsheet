<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class RefTest extends TestCase
{
    public function testREF(): void
    {
        $result = ExcelError::REF();
        self::assertEquals('#REF!', $result);
    }
}
