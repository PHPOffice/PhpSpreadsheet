<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NormSDist2Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerNORMSDIST2
     */
    public function testNORMSDIST2(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('NORM.S.DIST', $expectedResult, ...$args);
    }

    public static function providerNORMSDIST2(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMSDIST2.php';
    }

    /**
     * @dataProvider providerNormSDist2Array
     */
    public function testNormSDist2Array(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NORM.S.DIST({$values}, true)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNormSDist2Array(): array
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
