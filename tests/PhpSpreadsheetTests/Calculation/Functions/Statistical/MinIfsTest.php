<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinIfsTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerMINIFS')]
    public function testMINIFS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('MINIFS', $expectedResult, ...$args);
    }

    public static function providerMINIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MINIFS.php';
    }
}
