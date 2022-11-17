<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class TBillEqTest extends TestCase
{
    /**
     * @dataProvider providerTBILLEQ
     *
     * @param mixed $expectedResult
     */
    public function testTBILLEQ($expectedResult, ...$args): void
    {
        $result = Financial\TreasuryBill::bondEquivalentYield(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLEQ(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLEQ.php';
    }
}
