<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselJTest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSEJ
     *
     * @param mixed $expectedResult
     */
    public function testBESSELJ($expectedResult, ...$args)
    {
        $result = Engineering::BESSELJ(...$args);
        $this->assertEquals($expectedResult, $result, '', self::BESSEL_PRECISION);
    }

    public function providerBESSEJ()
    {
        return require 'data/Calculation/Engineering/BESSELJ.php';
    }
}
