<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormSDistTest extends TestCase
{
    /**
     * @dataProvider providerNORMSDIST
     *
     * @param mixed $expectedResult
     * @param mixed $testValue
     */
    public function testNORMSDIST($expectedResult, $testValue): void
    {
        $result = Statistical::NORMSDIST($testValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMSDIST(): array
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

    public function providerNormSDistArray(): array
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
