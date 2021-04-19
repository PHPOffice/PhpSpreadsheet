<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DollarFrTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDOLLARFR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARFR($expectedResult, ...$args): void
    {
        $result = Financial::DOLLARFR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDOLLARFR(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARFR.php';
    }
}
