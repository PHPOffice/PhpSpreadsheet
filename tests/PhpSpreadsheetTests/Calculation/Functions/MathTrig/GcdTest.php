<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class GcdTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGCD
     *
     * @param mixed $expectedResult
     */
    public function testGCD($expectedResult, ...$args)
    {
        $result = MathTrig::GCD(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerGCD()
    {
        return require 'data/Calculation/MathTrig/GCD.php';
    }
}
