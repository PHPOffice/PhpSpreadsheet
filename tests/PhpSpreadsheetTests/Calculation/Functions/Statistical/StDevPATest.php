<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class StDevPATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSTDEVPA
     */
    public function testSTDEVPA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('STDEVPA', $expectedResult, ...$args);
    }

    public static function providerSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA.php';
    }

    /**
     * @dataProvider providerOdsSTDEVPA
     */
    public function testOdsSTDEVPA(mixed $expectedResult, mixed ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCaseReference('STDEVPA', $expectedResult, ...$args);
    }

    public static function providerOdsSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA_ODS.php';
    }
}
