<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class ArabicTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerARABIC
     *
     * @param mixed $expectedResult
     * @param string $romanNumeral
     */
    public function testARABIC($expectedResult, $romanNumeral)
    {
        $result = MathTrig::ARABIC($romanNumeral);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerARABIC()
    {
        return require 'data/Calculation/MathTrig/ARABIC.php';
    }
}
