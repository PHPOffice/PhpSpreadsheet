<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class RsqTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRSQ
     *
     * @param mixed $expectedResult
     */
    public function testRSQ($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('RSQ', $expectedResult, ...$args);
    }

    public static function providerRSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/RSQ.php';
    }
}
