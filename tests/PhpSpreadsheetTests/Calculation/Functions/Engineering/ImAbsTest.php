<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImAbsTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMABS($expectedResult, $value)
    {
        $result = Engineering::IMABS($value);
        $this->assertEquals($expectedResult, $result, '', self::COMPLEX_PRECISION);
    }

    public function providerIMABS()
    {
        return require 'data/Calculation/Engineering/IMABS.php';
    }
}
