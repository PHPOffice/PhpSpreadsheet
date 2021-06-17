<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiTestTest extends TestCase
{
    /**
     * @dataProvider providerCHITEST
     *
     * @param mixed $expectedResult
     * @param mixed $actual
     * @param mixed $expected
     */
    public function testCHITEST($expectedResult, $actual, $expected): void
    {
        $result = Statistical\Distributions\ChiSquared::test($actual, $expected);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHITEST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHITEST.php';
    }
}
