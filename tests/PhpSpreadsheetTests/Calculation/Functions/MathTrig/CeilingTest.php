<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CeilingTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     */
    public function testCEILING($expectedResult, ...$args)
    {
        $result = MathTrig::CEILING(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCEILING()
    {
        return require 'data/Calculation/MathTrig/CEILING.php';
    }
}
