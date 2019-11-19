<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GeoMeanTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGEOMEAN
     *
     * @param mixed $expectedResult
     */
    public function testGEOMEAN($expectedResult, ...$args)
    {
        $result = Statistical::GEOMEAN(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerGEOMEAN()
    {
        return require 'data/Calculation/Statistical/GEOMEAN.php';
    }
}
