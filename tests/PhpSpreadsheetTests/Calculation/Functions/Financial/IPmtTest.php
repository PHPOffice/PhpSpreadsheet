<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IPmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIPMT
     *
     * @param mixed $expectedResult
     */
    public function testIPMT($expectedResult, array $args): void
    {
        $this->runTestCase('IPMT', $expectedResult, $args);
    }

    public static function providerIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }
}
