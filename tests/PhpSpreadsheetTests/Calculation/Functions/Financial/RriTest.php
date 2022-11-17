<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class RriTest extends TestCase
{
    /**
     * @dataProvider providerRRI
     *
     * @param mixed $expectedResult
     */
    public function testRRI($expectedResult, array $args): void
    {
        if (count($args) === 0) {
            $result = Financial\CashFlow\Single::interestRate();
        } elseif (count($args) === 1) {
            $result = Financial\CashFlow\Single::interestRate($args[0]);
        } elseif (count($args) === 2) {
            $result = Financial\CashFlow\Single::interestRate($args[0], $args[1]);
        } else {
            $result = Financial\CashFlow\Single::interestRate($args[0], $args[1], $args[2]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRRI(): array
    {
        return require 'tests/data/Calculation/Financial/RRI.php';
    }
}
