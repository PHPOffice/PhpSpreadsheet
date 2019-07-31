<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitXorTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITXOR($expectedResult, array $args)
    {
        $result = Engineering::BITXOR(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBITXOR()
    {
        return require 'data/Calculation/Engineering/BITXOR.php';
    }
}
