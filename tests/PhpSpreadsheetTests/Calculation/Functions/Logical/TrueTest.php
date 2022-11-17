<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class TrueTest extends TestCase
{
    public function testTRUE(): void
    {
        $result = Logical\Boolean::TRUE();
        self::assertTrue($result);
    }
}
