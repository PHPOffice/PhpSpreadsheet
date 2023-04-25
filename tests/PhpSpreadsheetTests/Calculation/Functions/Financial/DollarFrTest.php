<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DollarFrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDOLLARFR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARFR($expectedResult, ...$args): void
    {
        $this->runTestCase('DOLLARFR', $expectedResult, $args);
    }

    public static function providerDOLLARFR(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARFR.php';
    }
}
