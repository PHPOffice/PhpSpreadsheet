<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class NpvTest extends TestCase
{
    /**
     * @dataProvider providerNPV
     *
     * @param mixed $expectedResult
     */
    public function testNPV($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Variable\Periodic::presentValue(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNPV(): array
    {
        return require 'tests/data/Calculation/Financial/NPV.php';
    }
}
