<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ForecastTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFORECAST
     *
     * @param mixed $expectedResult
     */
    public function testFORECAST($expectedResult, ...$args)
    {
        $result = Statistical::FORECAST(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerFORECAST()
    {
        return require 'data/Calculation/Statistical/FORECAST.php';
    }
}
