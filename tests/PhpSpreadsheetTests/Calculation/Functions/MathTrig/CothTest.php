<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CothTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOTH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCOTH($expectedResult, $angle)
    {
        $result = MathTrig::COTH($angle);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCOTH()
    {
        return require 'data/Calculation/MathTrig/COTH.php';
    }
}
