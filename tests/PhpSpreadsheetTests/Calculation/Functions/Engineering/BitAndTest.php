<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitAndTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITAND($expectedResult, array $args)
    {
        $result = Engineering::BITAND(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBITAND()
    {
        return require 'data/Calculation/Engineering/BITAND.php';
    }
}
