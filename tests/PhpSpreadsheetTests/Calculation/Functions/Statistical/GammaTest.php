<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaTest extends TestCase
{
    /**
     * @dataProvider providerGAMMA
     *
     * @param mixed $expectedResult
     * @param mixed $testValue
     */
    public function testGAMMA($expectedResult, $testValue): void
    {
        $result = Statistical::GAMMAFunction($testValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMA(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMA.php';
    }
}
