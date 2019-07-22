<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class ModTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMOD
     *
     * @param mixed $expectedResult
     */
    public function testMOD($expectedResult, ...$args)
    {
        $result = MathTrig::MOD(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMOD()
    {
        return require 'data/Calculation/MathTrig/MOD.php';
    }
}
