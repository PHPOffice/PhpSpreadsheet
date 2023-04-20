<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillPriceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTBILLPRICE
     *
     * @param mixed $expectedResult
     */
    public function testTBILLPRICE($expectedResult, ...$args): void
    {
        $this->runTestCase('TBILLPRICE', $expectedResult, $args);
    }

    public static function providerTBILLPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLPRICE.php';
    }
}
