<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class RomanTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     */
    public function testROMAN($expectedResult, ...$args)
    {
        $result = MathTrig::ROMAN(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return require 'data/Calculation/MathTrig/ROMAN.php';
    }
}
