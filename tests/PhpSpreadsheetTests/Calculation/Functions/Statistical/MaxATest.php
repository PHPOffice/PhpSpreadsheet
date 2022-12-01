<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMAXA
     *
     * @param mixed $expectedResult
     */
    public function testMAXA($expectedResult, ...$args): void
    {
        $this->runTestCases('MAXA', $expectedResult, ...$args);
    }

    public function providerMAXA(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXA.php';
    }
}
