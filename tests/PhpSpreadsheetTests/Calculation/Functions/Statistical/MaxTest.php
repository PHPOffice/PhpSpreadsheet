<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerMAX')]
    public function testMAX(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('MAX', $expectedResult, ...$args);
    }

    public static function providerMAX(): array
    {
        return require 'tests/data/Calculation/Statistical/MAX.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerMAXLiteralArguments')]
    public function testMAXLiteralArguments(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseDirect('MAX', $expectedResult, ...$args);
    }

    public static function providerMAXLiteralArguments(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXLiteralArguments.php';
    }
}
