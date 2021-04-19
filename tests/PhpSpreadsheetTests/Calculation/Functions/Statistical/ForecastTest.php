<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ForecastTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFORECAST
     *
     * @param mixed $expectedResult
     */
    public function testFORECAST($expectedResult, ...$args): void
    {
        $result = Statistical::FORECAST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFORECAST(): array
    {
        return require 'tests/data/Calculation/Statistical/FORECAST.php';
    }
}
