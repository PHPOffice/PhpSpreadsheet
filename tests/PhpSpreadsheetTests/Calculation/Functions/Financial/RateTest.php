<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class RateTest extends TestCase
{
    /**
     * @dataProvider providerRATE
     *
     * @param mixed $expectedResult
     */
    public function testRATE($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Interest::rate(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRATE(): array
    {
        return require 'tests/data/Calculation/Financial/RATE.php';
    }
}
