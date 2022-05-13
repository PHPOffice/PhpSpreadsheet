<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;
use PHPUnit\Framework\TestCase;

class NPerTest extends TestCase
{
    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, array $args = []): void
    {
        $result = Periodic::periods(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}
