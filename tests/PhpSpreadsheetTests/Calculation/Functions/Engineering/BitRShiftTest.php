<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitRShiftTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITRSHIFT($expectedResult, array $args)
    {
        $result = Engineering::BITRSHIFT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBITRSHIFT()
    {
        return require 'data/Calculation/Engineering/BITRSHIFT.php';
    }
}
