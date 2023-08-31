<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

// TODO There are some commented out cases which don't return correct value
class CountIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTIFS
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIFS($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('COUNTIFS', $expectedResult, ...$args);
    }

    public static function providerCOUNTIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIFS.php';
    }
}
