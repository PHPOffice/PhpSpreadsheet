<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class PercentileTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPERCENTILE
     *
     * @param mixed $expectedResult
     */
    public function testPERCENTILE($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('PERCENTILE', $expectedResult, ...$args);
    }

    public static function providerPERCENTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/PERCENTILE.php';
    }
}
