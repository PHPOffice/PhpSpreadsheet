<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SlopeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSLOPE
     *
     * @param mixed $expectedResult
     */
    public function testSLOPE($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('SLOPE', $expectedResult, ...$args);
    }

    public static function providerSLOPE(): array
    {
        return require 'tests/data/Calculation/Statistical/SLOPE.php';
    }
}
