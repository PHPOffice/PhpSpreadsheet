<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMAX
     *
     * @param mixed $expectedResult
     */
    public function testMAX($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('MAX', $expectedResult, ...$args);
    }

    public static function providerMAX(): array
    {
        return require 'tests/data/Calculation/Statistical/MAX.php';
    }
}
