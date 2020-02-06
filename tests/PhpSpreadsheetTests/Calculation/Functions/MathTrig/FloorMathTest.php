<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FloorMathTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFLOORMATH
     *
     * @param mixed $expectedResult
     */
    public function testFLOORMATH($expectedResult, ...$args)
    {
        $result = MathTrig::FLOORMATH(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerFLOORMATH()
    {
        return require 'data/Calculation/MathTrig/FLOORMATH.php';
    }
}
