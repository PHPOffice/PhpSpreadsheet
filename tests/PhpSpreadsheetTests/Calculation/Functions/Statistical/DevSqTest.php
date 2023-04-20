<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class DevSqTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDEVSQ
     *
     * @param mixed $expectedResult
     */
    public function testDEVSQ($expectedResult, ...$args): void
    {
        $this->runTestCases('DEVSQ', $expectedResult, ...$args);
    }

    public static function providerDEVSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/DEVSQ.php';
    }
}
