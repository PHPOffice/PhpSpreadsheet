<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;
use PHPUnit\Framework\TestCase;

class PvTest extends TestCase
{
    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args = []): void
    {
        $result = Periodic::presentValue(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}
