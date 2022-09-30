<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;
use PHPUnit\Framework\TestCase;

class FvTest extends TestCase
{
    /**
     * @dataProvider providerFV
     *
     * @param mixed $expectedResult
     */
    public function testFV($expectedResult, array $args = []): void
    {
        $result = Periodic::futureValue(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFV(): array
    {
        return require 'tests/data/Calculation/Financial/FV.php';
    }
}
