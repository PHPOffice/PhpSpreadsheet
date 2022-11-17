<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class FalseTest extends TestCase
{
    public function testFALSE(): void
    {
        $result = Logical\Boolean::FALSE();
        self::assertFalse($result);
    }
}
