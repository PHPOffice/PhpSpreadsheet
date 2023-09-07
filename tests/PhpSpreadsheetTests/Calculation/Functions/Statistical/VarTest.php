<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVAR
     */
    public function testVAR(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('VAR', $expectedResult, ...$args);
    }

    public static function providerVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR.php';
    }

    /**
     * @dataProvider providerOdsVAR
     */
    public function testOdsVAR(mixed $expectedResult, mixed ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VAR', $expectedResult, ...$args);
    }

    public static function providerOdsVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR_ODS.php';
    }
}
