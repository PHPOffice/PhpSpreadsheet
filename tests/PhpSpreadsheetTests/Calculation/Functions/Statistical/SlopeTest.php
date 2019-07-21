<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class SlopeTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSLOPE
     *
     * @param mixed $expectedResult
     */
    public function testSLOPE($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::SLOPE($xargs, $yargs);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSLOPE()
    {
        return require 'data/Calculation/Statistical/SLOPE.php';
    }
}
