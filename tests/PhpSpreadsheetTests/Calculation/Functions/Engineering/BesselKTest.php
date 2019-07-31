<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselKTest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSELK
     *
     * @param mixed $expectedResult
     */
    public function testBESSELK($expectedResult, ...$args)
    {
        $result = Engineering::BESSELK(...$args);
        $this->assertEquals($expectedResult, $result, '', self::BESSEL_PRECISION);
    }

    public function providerBESSELK()
    {
        return require 'data/Calculation/Engineering/BESSELK.php';
    }
}
