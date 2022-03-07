<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMAINV
     *
     * @param mixed $expectedResult
     */
    public function testGAMMAINV($expectedResult, ...$args): void
    {
        $result = Statistical::GAMMAINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMAINV.php';
    }

    /**
     * @dataProvider providerGammaInvArray
     */
    public function testGammaInvArray(array $expectedResult, string $values, string $alpha, string $beta): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GAMMA.INV({$values}, {$alpha}, {$beta})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerGammaInvArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [2.772588722239782, 5.38526905777939, 12.548861396889375],
                    [5.545177444479563, 10.77053811555878, 25.09772279377875],
                    [6.931471805599453, 13.463172644448473, 31.372153492223436],
                ],
                '0.75',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
