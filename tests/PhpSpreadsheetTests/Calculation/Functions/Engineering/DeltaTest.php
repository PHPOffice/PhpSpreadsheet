<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DeltaTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDELTA
     *
     * @param mixed $expectedResult
     */
    public function testDELTA($expectedResult, ...$args)
    {
        $result = Engineering::DELTA(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDELTA()
    {
        return require 'data/Calculation/Engineering/DELTA.php';
    }
}
