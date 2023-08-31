<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEVP
     *
     * @param mixed $expectedResult
     */
    public function testSTDEVP($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('STDEVP', $expectedResult, ...$args);
    }

    public static function providerSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP.php';
    }

    /**
     * @dataProvider providerOdsSTDEVP
     *
     * @param mixed $expectedResult
     */
    public function testOdsSTDEVP($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEVP', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP_ODS.php';
    }
}
