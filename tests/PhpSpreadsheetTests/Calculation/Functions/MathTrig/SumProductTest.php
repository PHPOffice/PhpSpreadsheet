<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SumProductTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testSUMPRODUCT($expectedResult, ...$args)
    {
        $result = MathTrig::SUMPRODUCT(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUMPRODUCT()
    {
        return require 'data/Calculation/MathTrig/SUMPRODUCT.php';
    }
}
