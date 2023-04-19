<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEVA
     *
     * @param mixed $expectedResult
     */
    public function testSTDEVA($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('STDEVA', $expectedResult, ...$args);
    }

    public static function providerSTDEVA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVA.php';
    }

    /**
     * @dataProvider providerOdsSTDEVA
     *
     * @param mixed $expectedResult
     */
    public function testOdsSTDEVA($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEVA', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEVA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVA_ODS.php';
    }
}
