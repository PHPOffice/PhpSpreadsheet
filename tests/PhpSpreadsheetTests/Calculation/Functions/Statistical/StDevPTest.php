<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEVP
     */
    public function testSTDEVP(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('STDEVP', $expectedResult, ...$args);
    }

    public static function providerSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP.php';
    }

    /**
     * @dataProvider providerOdsSTDEVP
     */
    public function testOdsSTDEVP(mixed $expectedResult, mixed ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEVP', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEVP(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVP_ODS.php';
    }
}
