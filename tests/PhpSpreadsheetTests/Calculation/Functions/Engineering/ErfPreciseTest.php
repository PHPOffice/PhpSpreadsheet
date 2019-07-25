<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ErfPreciseTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerERFPRECISE
     *
     * @param mixed $expectedResult
     */
    public function testERFPRECISE($expectedResult, ...$args)
    {
        $result = Engineering::ERFPRECISE(...$args);
        $this->assertEquals($expectedResult, $result, '', self::ERF_PRECISION);
    }

    public function providerERFPRECISE()
    {
        return require 'data/Calculation/Engineering/ERFPRECISE.php';
    }
}
