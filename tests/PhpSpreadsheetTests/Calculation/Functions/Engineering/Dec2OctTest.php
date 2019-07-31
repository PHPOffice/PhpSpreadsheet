<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Dec2OctTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDEC2OCT
     *
     * @param mixed $expectedResult
     */
    public function testDEC2OCT($expectedResult, ...$args)
    {
        $result = Engineering::DECTOOCT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDEC2OCT()
    {
        return require 'data/Calculation/Engineering/DEC2OCT.php';
    }
}
