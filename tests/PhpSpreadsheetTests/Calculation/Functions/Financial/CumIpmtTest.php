<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CumIpmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCUMIPMT
     *
     * @param mixed $expectedResult
     */
    public function testCUMIPMT($expectedResult, ...$args): void
    {
        $this->runTestCase('CUMIPMT', $expectedResult, $args);
    }

    public static function providerCUMIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/CUMIPMT.php';
    }
}
