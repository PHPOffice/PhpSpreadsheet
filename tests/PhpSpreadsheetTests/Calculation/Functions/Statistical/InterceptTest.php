<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class InterceptTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINTERCEPT
     *
     * @param mixed $expectedResult
     */
    public function testINTERCEPT($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('INTERCEPT', $expectedResult, ...$args);
    }

    public static function providerINTERCEPT(): array
    {
        return require 'tests/data/Calculation/Statistical/INTERCEPT.php';
    }
}
