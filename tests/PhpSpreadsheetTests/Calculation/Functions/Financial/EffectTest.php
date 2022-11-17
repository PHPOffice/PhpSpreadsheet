<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class EffectTest extends TestCase
{
    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     * @param mixed $rate
     * @param mixed $periods
     */
    public function testEFFECT($expectedResult, $rate, $periods): void
    {
        $result = Financial\InterestRate::effective($rate, $periods);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEFFECT(): array
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }
}
