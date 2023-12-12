<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class GeoMeanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGEOMEAN
     */
    public function testGEOMEAN(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GEOMEAN', $expectedResult, ...$args);
    }

    public static function providerGEOMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/GEOMEAN.php';
    }
}
