<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINIFS
     *
     * @param mixed $expectedResult
     */
    public function testMINIFS($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('MINIFS', $expectedResult, ...$args);
    }

    public static function providerMINIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MINIFS.php';
    }
}
