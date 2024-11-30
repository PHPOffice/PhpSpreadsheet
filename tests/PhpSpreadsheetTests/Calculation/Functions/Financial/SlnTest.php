<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class SlnTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerSLN')]
    public function testSLN(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('SLN', $expectedResult, $args);
    }

    public static function providerSLN(): array
    {
        return require 'tests/data/Calculation/Financial/SLN.php';
    }
}
