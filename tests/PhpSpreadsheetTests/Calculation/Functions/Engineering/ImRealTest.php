<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImRealTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMREAL
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMREAL($expectedResult, $value)
    {
        $result = Engineering::IMREAL($value);
        $this->assertEquals($expectedResult, $result, '', self::COMPLEX_PRECISION);
    }

    public function providerIMREAL()
    {
        return require 'data/Calculation/Engineering/IMREAL.php';
    }
}
