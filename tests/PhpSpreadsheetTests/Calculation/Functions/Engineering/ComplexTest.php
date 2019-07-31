<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEX($expectedResult, ...$args)
    {
        $result = Engineering::COMPLEX(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX()
    {
        return require 'data/Calculation/Engineering/COMPLEX.php';
    }
}
