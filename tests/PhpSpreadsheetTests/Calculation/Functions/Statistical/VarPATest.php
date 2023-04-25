<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarPATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVARPA
     *
     * @param mixed $expectedResult
     */
    public function testVARPA($expectedResult, ...$args): void
    {
        $this->runTestCases('VARPA', $expectedResult, ...$args);
    }

    public static function providerVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA.php';
    }

    /**
     * @dataProvider providerOdsVARPA
     *
     * @param mixed $expectedResult
     */
    public function testOdsVARPA($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VARPA', $expectedResult, ...$args);
    }

    public static function providerOdsVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA_ODS.php';
    }
}
