<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class IntTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINT
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testINT($expectedResult, $value)
    {
        $result = MathTrig::INT($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return require 'data/Calculation/MathTrig/INT.php';
    }
}
