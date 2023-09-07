<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IPmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIPMT
     */
    public function testIPMT(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('IPMT', $expectedResult, $args);
    }

    public static function providerIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }
}
