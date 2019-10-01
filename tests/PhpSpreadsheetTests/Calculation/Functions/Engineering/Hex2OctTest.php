<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Hex2OctTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHEX2OCT
     *
     * @param mixed $expectedResult
     */
    public function testHEX2OCT($expectedResult, ...$args)
    {
        $result = Engineering::HEXTOOCT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHEX2OCT()
    {
        return require 'data/Calculation/Engineering/HEX2OCT.php';
    }
}
