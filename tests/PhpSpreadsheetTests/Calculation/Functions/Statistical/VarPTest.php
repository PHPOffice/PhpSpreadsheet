<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class VarPTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerVARP')]
    public function testVARP(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('VARP', $expectedResult, ...$args);
    }

    public static function providerVARP(): array
    {
        return require 'tests/data/Calculation/Statistical/VARP.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerOdsVARP')]
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
