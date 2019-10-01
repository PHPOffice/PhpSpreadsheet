<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SumX2MY2Test extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMX2MY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMX2MY2($expectedResult, ...$args)
    {
        $result = MathTrig::SUMX2MY2(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUMX2MY2()
    {
        return require 'data/Calculation/MathTrig/SUMX2MY2.php';
    }
}
