<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class LcmTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLCM
     *
     * @param mixed $expectedResult
     */
    public function testLCM($expectedResult, ...$args)
    {
        $result = MathTrig::LCM(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerLCM()
    {
        return require 'data/Calculation/MathTrig/LCM.php';
    }
}
