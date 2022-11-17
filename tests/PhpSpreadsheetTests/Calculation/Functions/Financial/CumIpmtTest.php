<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CumIpmtTest extends TestCase
{
    /**
     * @dataProvider providerCUMIPMT
     *
     * @param mixed $expectedResult
     */
    public function testCUMIPMT($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Cumulative::interest(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/CUMIPMT.php';
    }
}
