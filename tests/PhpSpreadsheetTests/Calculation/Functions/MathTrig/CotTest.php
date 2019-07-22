<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CotTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOT
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCOT($expectedResult, $angle)
    {
        $result = MathTrig::COT($angle);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOT()
    {
        return require 'data/Calculation/MathTrig/COT.php';
    }
}
