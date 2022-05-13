<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;
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
            $result = Single::interestRate();
        } elseif (count($args) === 1) {
            $result = Single::interestRate($args[0]);
        } elseif (count($args) === 2) {
            $result = Single::interestRate($args[0], $args[1]);
        } else {
            $result = Single::interestRate($args[0], $args[1], $args[2]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRRI(): array
    {
        return require 'tests/data/Calculation/Financial/RRI.php';
    }
}
