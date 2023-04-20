<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPMT
     *
     * @param mixed $expectedResult
     */
    public function testPMT($expectedResult, array $args): void
    {
        $this->runTestCase('PMT', $expectedResult, $args);
    }

    public static function providerPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PMT.php';
    }
}
