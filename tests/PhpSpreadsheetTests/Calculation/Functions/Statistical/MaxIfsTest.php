<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMAXIFS
     *
     * @param mixed $expectedResult
     */
    public function testMAXIFS($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('MAXIFS', $expectedResult, ...$args);
    }

    public static function providerMAXIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXIFS.php';
    }
}
