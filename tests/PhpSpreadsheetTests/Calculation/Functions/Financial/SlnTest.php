<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class SlnTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerSLN')]
    public function testSLN(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('SLN', $expectedResult, $args);
    }

    public static function providerSLN(): array
    {
        return require 'tests/data/Calculation/Financial/SLN.php';
    }
}
