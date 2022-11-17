<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class CumPrincTest extends TestCase
{
    /**
     * @dataProvider providerCUMPRINC
     *
     * @param mixed $expectedResult
     */
    public function testCUMPRINC($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Cumulative::principal(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMPRINC(): array
    {
        return require 'tests/data/Calculation/Financial/CUMPRINC.php';
    }
}
