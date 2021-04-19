<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CoupNumTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUPNUM
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNUM($expectedResult, ...$args): void
    {
        $result = Financial::COUPNUM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNUM(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNUM.php';
    }
}
