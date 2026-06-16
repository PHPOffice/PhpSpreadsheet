<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class PDurationTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerPDURATION')]
    public function testPDURATION(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PDURATION', $expectedResult, $args);
    }

    public static function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
