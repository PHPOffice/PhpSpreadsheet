<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVAR
     *
     * @param mixed $expectedResult
     */
    public function testVAR($expectedResult, ...$args): void
    {
        $this->runTestCases('VAR', $expectedResult, ...$args);
    }

    public static function providerVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR.php';
    }

    /**
     * @dataProvider providerOdsVAR
     *
     * @param mixed $expectedResult
     */
    public function testOdsVAR($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VAR', $expectedResult, ...$args);
    }

    public static function providerOdsVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR_ODS.php';
    }
}
