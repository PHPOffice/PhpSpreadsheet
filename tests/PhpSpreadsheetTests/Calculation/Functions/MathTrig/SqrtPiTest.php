<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SqrtPiTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testSQRTPI($expectedResult, $value)
    {
        $result = MathTrig::SQRTPI($value);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSQRTPI()
    {
        return require 'data/Calculation/MathTrig/SQRTPI.php';
    }
}
