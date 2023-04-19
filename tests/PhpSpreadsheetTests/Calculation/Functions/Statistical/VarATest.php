<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVARA
     *
     * @param mixed $expectedResult
     */
    public function testVARA($expectedResult, ...$args): void
    {
        $this->runTestCases('VARA', $expectedResult, ...$args);
    }

    public static function providerVARA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARA.php';
    }

    /**
     * @dataProvider providerOdsVARA
     *
     * @param mixed $expectedResult
     */
    public function testOdsVARA($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VARA', $expectedResult, ...$args);
    }

    public static function providerOdsVARA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARA_ODS.php';
    }
}
