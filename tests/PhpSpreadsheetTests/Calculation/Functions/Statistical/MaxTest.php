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
        $this->runTestCases('MAX', $expectedResult, ...$args);
    }

    public function providerMAX(): array
    {
        return require 'tests/data/Calculation/Statistical/MAX.php';
    }
}
