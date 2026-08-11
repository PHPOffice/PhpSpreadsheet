<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class ReceivedTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerRECEIVED')]
    public function testRECEIVED(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('RECEIVED', $expectedResult, $args);
    }

    public static function providerRECEIVED(): array
    {
        return require 'tests/data/Calculation/Financial/RECEIVED.php';
    }
}
