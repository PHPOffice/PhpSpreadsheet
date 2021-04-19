<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IntRateTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINTRATE
     *
     * @param mixed $expectedResult
     */
    public function testINTRATE($expectedResult, ...$args): void
    {
        $result = Financial::INTRATE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerINTRATE(): array
    {
        return require 'tests/data/Calculation/Financial/INTRATE.php';
    }
}
