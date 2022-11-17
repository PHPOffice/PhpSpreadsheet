<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class NominalTest extends TestCase
{
    /**
     * @dataProvider providerNOMINAL
     *
     * @param mixed $expectedResult
     * @param mixed $rate
     * @param mixed $periods
     */
    public function testNOMINAL($expectedResult, $rate, $periods): void
    {
        $result = Financial\InterestRate::nominal($rate, $periods);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNOMINAL(): array
    {
        return require 'tests/data/Calculation/Financial/NOMINAL.php';
    }
}
