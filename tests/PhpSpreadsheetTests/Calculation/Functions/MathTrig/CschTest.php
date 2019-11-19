<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CschTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCSCH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSCH($expectedResult, $angle)
    {
        $result = MathTrig::CSCH($angle);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCSCH()
    {
        return require 'data/Calculation/MathTrig/CSCH.php';
    }
}
