<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CoupPcdTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUPPCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPPCD($expectedResult, ...$args): void
    {
        $result = Coupons::COUPPCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPPCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPPCD.php';
    }
}
