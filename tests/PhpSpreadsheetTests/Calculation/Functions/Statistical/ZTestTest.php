<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ZTestTest extends TestCase
{
    /**
     * @dataProvider providerZTEST
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $dataSet
     * @param null|mixed $sigma
     */
    public function testZTEST($expectedResult, $dataSet, $value, $sigma = null): void
    {
        $result = Statistical::ZTEST($dataSet, $value, $sigma);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerZTEST(): array
    {
        return require 'tests/data/Calculation/Statistical/ZTEST.php';
    }

    /**
     * @dataProvider providerZTestArray
     */
    public function testZTestArray(array $expectedResult, string $dataSet, string $m0): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ZTEST({$dataSet}, {$m0})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerZTestArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.09057419685136381, 0.4516213175273426, 0.8630433891295299],
                ],
                '{3, 6, 7, 8, 6, 5, 4, 2, 1, 9}',
                '{4, 5, 6}',
            ],
        ];
    }
}
