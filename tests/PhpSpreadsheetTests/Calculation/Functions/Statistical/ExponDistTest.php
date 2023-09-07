<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ExponDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEXPONDIST
     */
    public function testEXPONDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('EXPONDIST', $expectedResult, ...$args);
    }

    public static function providerEXPONDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/EXPONDIST.php';
    }

    /**
     * @dataProvider providerExponDistArray
     */
    public function testExponDistArray(array $expectedResult, string $values, string $lambdas): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EXPONDIST({$values}, {$lambdas}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerExponDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [1.646434908282079, 0.6693904804452895, 0.2721538598682374],
                    [1.353352832366127, 0.06737946999085467, 0.003354626279025118],
                ],
                '{0.2, 0.5, 0.8}',
                '{3; 10}',
            ],
        ];
    }
}
