<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillEqTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTBILLEQ
     *
     * @param mixed $expectedResult
     */
    public function testTBILLEQ($expectedResult, ...$args): void
    {
        $this->runTestCase('TBILLEQ', $expectedResult, $args);
    }

    public static function providerTBILLEQ(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLEQ.php';
    }
}
