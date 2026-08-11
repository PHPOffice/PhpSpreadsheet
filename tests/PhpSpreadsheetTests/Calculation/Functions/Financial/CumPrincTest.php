<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CumPrincTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerCUMPRINC')]
    public function testCUMPRINC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('CUMPRINC', $expectedResult, $args);
    }

    public static function providerCUMPRINC(): array
    {
        return require 'tests/data/Calculation/Financial/CUMPRINC.php';
    }
}
