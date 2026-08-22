<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillEqTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerTBILLEQ')]
    public function testTBILLEQ(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('TBILLEQ', $expectedResult, $args);
    }

    public static function providerTBILLEQ(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLEQ.php';
    }
}
