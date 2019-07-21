<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class InterceptTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINTERCEPT
     *
     * @param mixed $expectedResult
     */
    public function testINTERCEPT($expectedResult, array $xargs, array $yargs)
    {
        $result = Statistical::INTERCEPT($xargs, $yargs);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerINTERCEPT()
    {
        return require 'data/Calculation/Statistical/INTERCEPT.php';
    }
}
