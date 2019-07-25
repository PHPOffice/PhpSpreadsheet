<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Dec2BinTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDEC2BIN
     *
     * @param mixed $expectedResult
     */
    public function testDEC2BIN($expectedResult, ...$args)
    {
        $result = Engineering::DECTOBIN(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDEC2BIN()
    {
        return require 'data/Calculation/Engineering/DEC2BIN.php';
    }
}
