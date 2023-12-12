<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class InterceptTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINTERCEPT
     */
    public function testINTERCEPT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('INTERCEPT', $expectedResult, ...$args);
    }

    public static function providerINTERCEPT(): array
    {
        return require 'tests/data/Calculation/Statistical/INTERCEPT.php';
    }
}
