<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class LogInvTest extends TestCase
{
    /**
     * @dataProvider providerLOGINV
     *
     * @param mixed $expectedResult
     */
    public function testLOGINV($expectedResult, ...$args): void
    {
        $result = Statistical::LOGINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOGINV(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGINV.php';
    }
}
