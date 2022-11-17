<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupNumTest extends TestCase
{
    /**
     * @dataProvider providerCOUPNUM
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNUM($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPNUM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNUM(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNUM.php';
    }
}
