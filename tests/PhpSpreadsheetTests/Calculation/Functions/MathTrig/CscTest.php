<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CscTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCSC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSC($expectedResult, $angle)
    {
        $result = MathTrig::CSC($angle);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCSC()
    {
        return require 'data/Calculation/MathTrig/CSC.php';
    }
}
