<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class OddTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerODD
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testODD($expectedResult, $value)
    {
        $result = MathTrig::ODD($value);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerODD()
    {
        return require 'data/Calculation/MathTrig/ODD.php';
    }
}
