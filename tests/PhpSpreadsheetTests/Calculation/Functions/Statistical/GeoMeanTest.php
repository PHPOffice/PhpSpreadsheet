<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class GeoMeanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGEOMEAN
     *
     * @param mixed $expectedResult
     */
    public function testGEOMEAN($expectedResult, ...$args): void
    {
        $this->runTestCases('GEOMEAN', $expectedResult, ...$args);
    }

    public static function providerGEOMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/GEOMEAN.php';
    }
}
