<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class GammaInvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGAMMAINV
     */
    public function testGAMMAINV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('GAMMA.INV', $expectedResult, ...$args);
    }

    public static function providerGAMMAINV(): array
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

    public static function providerGammaInvArray(): array
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
