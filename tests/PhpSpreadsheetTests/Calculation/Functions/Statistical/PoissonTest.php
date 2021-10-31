<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class PoissonTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPOISSON
     *
     * @param mixed $expectedResult
     */
    public function testPOISSON($expectedResult, ...$args): void
    {
        $result = Statistical::POISSON(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPOISSON(): array
    {
        return require 'tests/data/Calculation/Statistical/POISSON.php';
    }
}
