<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class HarMeanTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHarMean
     *
     * @param mixed $expectedResult
     */
    public function testHarMean($expectedResult, ...$args)
    {
        $result = Statistical::HarMean(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerHarMean()
    {
        return require 'data/Calculation/Statistical/HarMean.php';
    }
}
