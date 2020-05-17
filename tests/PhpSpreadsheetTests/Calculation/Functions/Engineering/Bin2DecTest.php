<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class Bin2DecTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBIN2DEC
     *
     * @param mixed $expectedResult
     */
    public function testBIN2DEC($expectedResult, ...$args)
    {
        $result = Engineering::BINTODEC(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBIN2DEC()
    {
        return require 'tests/data/Calculation/Engineering/BIN2DEC.php';
    }
}
