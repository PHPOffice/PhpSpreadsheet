<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GeoMeanTest extends TestCase
{
    protected function setUp(): void
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
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGEOMEAN()
    {
        return require 'tests/data/Calculation/Statistical/GEOMEAN.php';
    }
}
