<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Bin2HexTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBIN2HEX
     *
     * @param mixed $expectedResult
     */
    public function testBIN2HEX($expectedResult, ...$args)
    {
        $result = Engineering::BINTOHEX(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2HEX()
    {
        return require 'data/Calculation/Engineering/BIN2HEX.php';
    }
}
