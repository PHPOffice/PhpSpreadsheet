<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTA
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTA($expectedResult, ...$args): void
    {
        $this->runTestCases('COUNTA', $expectedResult, ...$args);
    }

    public static function providerCOUNTA(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTA.php';
    }
}
