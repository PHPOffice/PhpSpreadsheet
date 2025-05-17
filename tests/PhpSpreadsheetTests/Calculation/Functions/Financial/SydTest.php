<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class SydTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerSYD')]
    public function testSYD(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('SYD', $expectedResult, $args);
    }

    public static function providerSYD(): array
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}
