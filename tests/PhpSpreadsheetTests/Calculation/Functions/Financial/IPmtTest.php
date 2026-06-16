<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class IPmtTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerIPMT')]
    public function testIPMT(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('IPMT', $expectedResult, $args);
    }

    public static function providerIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }
}
