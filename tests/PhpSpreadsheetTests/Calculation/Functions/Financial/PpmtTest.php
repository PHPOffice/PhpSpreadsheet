<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class PpmtTest extends TestCase
{
    /**
     * @dataProvider providerPPMT
     *
     * @param mixed $expectedResult
     */
    public function testPPMT($expectedResult, array $args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Payments::interestPayment(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PPMT.php';
    }
}
