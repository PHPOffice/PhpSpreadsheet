<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEV
     *
     * @param mixed $expectedResult
     */
    public function testSTDEV($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('STDEV', $expectedResult, ...$args);
    }

    public static function providerSTDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEV.php';
    }

    /**
     * @dataProvider providerOdsSTDEV
     *
     * @param mixed $expectedResult
     */
    public function testOdsSTDEV($expectedResult, ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEV', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEV_ODS.php';
    }
}
