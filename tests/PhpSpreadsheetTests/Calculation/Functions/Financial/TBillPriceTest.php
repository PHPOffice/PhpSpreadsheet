<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillPriceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTBILLPRICE
     */
    public function testTBILLPRICE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('TBILLPRICE', $expectedResult, $args);
    }

    public static function providerTBILLPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLPRICE.php';
    }
}
