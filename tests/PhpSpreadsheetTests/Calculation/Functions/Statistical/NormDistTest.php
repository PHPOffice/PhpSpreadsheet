<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NormDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNORMDIST
     */
    public function testNORMDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('NORMDIST', $expectedResult, ...$args);
    }

    public static function providerNORMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMDIST.php';
    }

    /**
     * @dataProvider providerNormDistArray
     */
    public function testNormDistArray(array $expectedResult, string $values, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NORMDIST({$values}, {$mean}, {$stdDev}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNormDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.04324582990797181, 0.03549422283581691, 0.026885636057682592],
                    [0.07365402806066465, 0.038837210996642585, 0.015790031660178828],
                    [0.12098536225957167, 0.0022159242059690033, 7.991870553452737E-6],
                ],
                '12',
                '{10, 6, 3}',
                '{9; 5; 2}',
            ],
        ];
    }
}
