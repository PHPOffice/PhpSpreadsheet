<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class PercentileTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPERCENTILE
     *
     * @param mixed $expectedResult
     */
    public function testPERCENTILE($expectedResult, ...$args): void
    {
        $result = Statistical::PERCENTILE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPERCENTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/PERCENTILE.php';
    }
}
