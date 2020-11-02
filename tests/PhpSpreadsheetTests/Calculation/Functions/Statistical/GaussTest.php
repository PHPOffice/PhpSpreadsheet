<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GaussTest extends TestCase
{
    /**
     * @dataProvider providerGAUSS
     *
     * @param mixed $expectedResult
     * @param mixed $testValue
     */
    public function testGAUSS($expectedResult, $testValue): void
    {
        $result = Statistical::GAUSS($testValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAUSS(): array
    {
        return require 'tests/data/Calculation/Statistical/GAUSS.php';
    }
}
