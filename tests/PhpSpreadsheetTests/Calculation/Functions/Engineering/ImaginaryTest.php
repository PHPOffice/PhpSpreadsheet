<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ImaginaryTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMAGINARY($expectedResult, $value)
    {
        $result = Engineering::IMAGINARY($value);
        $this->assertEquals($expectedResult, $result, '', self::COMPLEX_PRECISION);
    }

    public function providerIMAGINARY()
    {
        return require 'data/Calculation/Engineering/IMAGINARY.php';
    }
}
