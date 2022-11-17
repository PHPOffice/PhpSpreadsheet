<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class TBillPriceTest extends TestCase
{
    /**
     * @dataProvider providerTBILLPRICE
     *
     * @param mixed $expectedResult
     */
    public function testTBILLPRICE($expectedResult, ...$args): void
    {
        $result = Financial\TreasuryBill::price(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLPRICE.php';
    }
}
