<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVARP
     */
    public function testVARP(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('VARP', $expectedResult, ...$args);
    }

    public static function providerVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP.php';
    }

    /**
     * @dataProvider providerOdsVARP
     */
    public function testOdsVARP(mixed $expectedResult, mixed ...$args): void
    {
        $this->setOpenOffice();
        $this->runTestCases('VARP', $expectedResult, ...$args);
    }

    public static function providerOdsVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP_ODS.php';
    }
}
