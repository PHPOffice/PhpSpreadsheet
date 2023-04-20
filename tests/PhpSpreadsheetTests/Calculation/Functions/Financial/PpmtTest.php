<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PpmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPPMT
     *
     * @param mixed $expectedResult
     */
    public function testPPMT($expectedResult, array $args): void
    {
        $this->runTestCase('PPMT', $expectedResult, $args);
    }

    public static function providerPPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PPMT.php';
    }
}
