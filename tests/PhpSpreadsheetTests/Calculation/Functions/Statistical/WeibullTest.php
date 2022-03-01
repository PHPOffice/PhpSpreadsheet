<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class WeibullTest extends TestCase
{
    /**
     * @dataProvider providerWEIBULL
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $alpha
     * @param mixed $beta
     * @param mixed $cumulative
     */
    public function testWEIBULL($expectedResult, $value, $alpha, $beta, $cumulative): void
    {
        $result = Statistical::WEIBULL($value, $alpha, $beta, $cumulative);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerWEIBULL(): array
    {
        return require 'tests/data/Calculation/Statistical/WEIBULL.php';
    }

    /**
     * @dataProvider providerWeibullArray
     */
    public function testWeibullArray(array $expectedResult, string $values, string $alpha, string $beta): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=WEIBULL({$values}, {$alpha}, {$beta}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerWeibullArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.18393972058572117, 0.36787944117144233, 0.9196986029286058],
                    [0.15163266492815836, 0.19470019576785122, 0.07572134644346439],
                    [0.13406400920712788, 0.1363430062345938, 0.0253391936076857],
                ],
                '2',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
