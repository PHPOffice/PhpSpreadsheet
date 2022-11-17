<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class TBillYieldTest extends TestCase
{
    /**
     * @dataProvider providerTBILLYIELD
     *
     * @param mixed $expectedResult
     */
    public function testTBILLYIELD($expectedResult, ...$args): void
    {
        $result = Financial\TreasuryBill::yield(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLYIELD(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLYIELD.php';
    }
}
