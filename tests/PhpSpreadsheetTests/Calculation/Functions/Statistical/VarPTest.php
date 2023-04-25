<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVARP
     *
     * @param mixed $expectedResult
     */
    public function testVARP($expectedResult, ...$args): void
    {
        $this->runTestCases('VARP', $expectedResult, ...$args);
    }

    public static function providerVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP.php';
    }

    /**
     * @dataProvider providerOdsVARP
     *
     * @param mixed $expectedResult
     */
    public function testOdsVARP($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VARP', $expectedResult, ...$args);
    }

    public static function providerOdsVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP_ODS.php';
    }
}
