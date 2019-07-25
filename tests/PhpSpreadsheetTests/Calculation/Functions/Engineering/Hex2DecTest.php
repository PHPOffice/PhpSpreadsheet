<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Hex2DecTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHEX2DEC
     *
     * @param mixed $expectedResult
     */
    public function testHEX2DEC($expectedResult, ...$args)
    {
        $result = Engineering::HEXTODEC(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHEX2DEC()
    {
        return require 'data/Calculation/Engineering/HEX2DEC.php';
    }
}
