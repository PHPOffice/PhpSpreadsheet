<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TrendTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGROWTH
     *
     * @param mixed $expectedResult
     */
    public function testTREND($expectedResult, ...$args): void
    {
        $result = Statistical::TREND(...$args);

        self::assertEqualsWithDelta($expectedResult, $result[0], 1E-12);
    }

    public function providerGROWTH()
    {
        return require 'tests/data/Calculation/Statistical/TREND.php';
    }
}
