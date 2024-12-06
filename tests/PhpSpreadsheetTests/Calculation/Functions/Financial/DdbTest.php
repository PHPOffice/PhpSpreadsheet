<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DdbTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerDDB')]
    public function testDDB(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('DDB', $expectedResult, $args);
    }

    public static function providerDDB(): array
    {
        return require 'tests/data/Calculation/Financial/DDB.php';
    }
}
