<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class TrueTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testTRUE(): void
    {
        $result = Logical::TRUE();
        self::assertTrue($result);
    }
}
