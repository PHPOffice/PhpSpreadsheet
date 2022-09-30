<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal;
use PHPUnit\Framework\TestCase;

class NormSDist2Test extends TestCase
{
    /**
     * @dataProvider providerNORMSDIST2
     *
     * @param mixed $expectedResult
     */
    public function testNORMSDIST2($expectedResult, ...$args): void
    {
        $result = StandardNormal::distribution(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMSDIST2(): array
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
        $result = $calculation->calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerNormSDist2Array(): array
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
