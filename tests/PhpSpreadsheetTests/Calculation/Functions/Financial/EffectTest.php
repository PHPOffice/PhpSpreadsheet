<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class EffectTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     * @param mixed $rate
     * @param mixed $periods
     */
    public function testEFFECT($expectedResult, $rate, $periods): void
    {
        $result = InterestRate::effective($rate, $periods);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEFFECT(): array
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }
}
