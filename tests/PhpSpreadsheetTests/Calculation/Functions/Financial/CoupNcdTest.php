<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CoupNcdTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUPNCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNCD($expectedResult, ...$args): void
    {
        $result = Financial::COUPNCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNCD.php';
    }
}
