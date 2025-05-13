<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class FvTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerFV')]
    public function testFV(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('FV', $expectedResult, $args);
    }

    public static function providerFV(): array
    {
        return require 'tests/data/Calculation/Financial/FV.php';
    }
}
