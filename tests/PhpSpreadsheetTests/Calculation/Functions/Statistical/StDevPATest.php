<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevPATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEVPA
     *
     * @param mixed $expectedResult
     */
    public function testSTDEVPA($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('STDEVPA', $expectedResult, ...$args);
    }

    public static function providerSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA.php';
    }

    /**
     * @dataProvider providerOdsSTDEVPA
     *
     * @param mixed $expectedResult
     */
    public function testOdsSTDEVPA($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEVPA', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA_ODS.php';
    }
}
