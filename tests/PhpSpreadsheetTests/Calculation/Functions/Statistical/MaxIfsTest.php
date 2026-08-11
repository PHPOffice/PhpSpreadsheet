<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxIfsTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerMAXIFS')]
    public function testMAXIFS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('MAXIFS', $expectedResult, ...$args);
    }

    public static function providerMAXIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXIFS.php';
    }
}
