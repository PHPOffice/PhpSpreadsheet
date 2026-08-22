<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class RsqTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerRSQ')]
    public function testRSQ(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('RSQ', $expectedResult, ...$args);
    }

    public static function providerRSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/RSQ.php';
    }
}
