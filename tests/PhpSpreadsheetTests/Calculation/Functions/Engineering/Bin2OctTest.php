<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Bin2OctTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBIN2OCT
     *
     * @param mixed $expectedResult
     */
    public function testBIN2OCT($expectedResult, ...$args)
    {
        $result = Engineering::BINTOOCT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2OCT()
    {
        return require 'tests/data/Calculation/Engineering/BIN2OCT.php';
    }
}
