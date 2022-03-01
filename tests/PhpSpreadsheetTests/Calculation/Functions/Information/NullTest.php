<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testNULL(): void
    {
        $result = Functions::null();
        self::assertEquals('#NULL!', $result);
    }
}
