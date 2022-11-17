<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CoupPcdTest extends TestCase
{
    /**
     * @dataProvider providerCOUPPCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPPCD($expectedResult, ...$args): void
    {
        $result = Financial\Coupons::COUPPCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPPCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPPCD.php';
    }
}
