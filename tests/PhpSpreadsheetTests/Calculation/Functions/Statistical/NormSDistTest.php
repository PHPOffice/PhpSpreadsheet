<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NormSDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNORMSDIST
     *
     * @param mixed $expectedResult
     */
    public function testNORMSDIST($expectedResult, ...$args): void
    {
        $this->runTestCases('NORMSDIST', $expectedResult, ...$args);
    }

    public static function providerNORMSDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMSDIST.php';
    }

    /**
     * @dataProvider providerNormSDistArray
     */
    public function testNormSDistArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NORMSDIST({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNormSDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.3085375387259869, 0.7733726476231317],
                    [0.99865010196837, 1.0],
                ],
                '{-0.5, 0.75; 3, 12.5}',
            ],
        ];
    }
}
