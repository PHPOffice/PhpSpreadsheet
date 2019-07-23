<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BesselITest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBESSELI
     *
     * @param mixed $expectedResult
     */
    public function testBESSELI($expectedResult, ...$args)
    {
        $result = Engineering::BESSELI(...$args);
        $this->assertEquals($expectedResult, $result, '', self::BESSEL_PRECISION);
    }

    public function providerBESSELI()
    {
        return require 'data/Calculation/Engineering/BESSELI.php';
    }
}
