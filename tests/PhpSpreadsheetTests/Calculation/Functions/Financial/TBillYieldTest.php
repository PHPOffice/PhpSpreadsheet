<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillYieldTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTBILLYIELD
     *
     * @param mixed $expectedResult
     */
    public function testTBILLYIELD($expectedResult, ...$args): void
    {
        $this->runTestCase('TBILLYIELD', $expectedResult, $args);
    }

    public static function providerTBILLYIELD(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLYIELD.php';
    }
}
