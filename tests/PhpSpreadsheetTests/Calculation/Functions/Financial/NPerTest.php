<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class NPerTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerNPER')]
    public function testNPER(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('NPER', $expectedResult, $args);
    }

    public static function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}
