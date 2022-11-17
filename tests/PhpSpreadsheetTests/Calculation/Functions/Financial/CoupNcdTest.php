<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupNcdTest extends TestCase
{
    /**
     * @dataProvider providerCOUPNCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNCD($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPNCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNCD.php';
    }
}
