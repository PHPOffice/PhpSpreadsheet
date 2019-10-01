<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SumXMY2Test extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMXMY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMXMY2($expectedResult, ...$args)
    {
        $result = MathTrig::SUMXMY2(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUMXMY2()
    {
        return require 'data/Calculation/MathTrig/SUMXMY2.php';
    }
}
