<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class Atan2Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     */
    public function testATAN2($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A3')->setValue(6);
        $sheet->getCell('A1')->setValue("=ATAN2($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerATAN2(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATAN2.php';
    }

    /**
     * @dataProvider providerAtan2Array
     */
    public function testAtan2Array(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ATAN2({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAtan2Array(): array
    {
        return [
            'first argument row vector' => [
                [[1.81577498992176, 1.17600520709514]],
                '{-0.75, 1.25}',
                '3',
            ],
            'first argument column vector' => [
                [[1.17600520709514], [0.98279372324733]],
                '{1.25; 2}',
                '3',
            ],
            'first argument matrix' => [
                [[2.03444393579570, 1.48765509490646], [1.57079632679490, 1.24904577239825]],
                '{-1.5, 0.25; 0, 1}',
                '3',
            ],
            'second argument row vector' => [
                [[-0.24497866312686, 0.39479111969976]],
                '3',
                '{-0.75, 1.25}',
            ],
            'second argument column vector' => [
                [[0.39479111969976], [0.58800260354757]],
                '3',
                '{1.25; 2}',
            ],
            'second argument matrix' => [
                [[-0.46364760900081, 0.08314123188844], [0.0, 0.32175055439664]],
                '3',
                '{-1.5, 0.25; 0, 1}',
            ],
            'A row and a column vector' => [
                [
                    [-2.21429743558818, 2.81984209919315, 2.55359005004223, 1.92956699706547],
                    [-1.69515132134166, 2.03444393579570, 1.81577498992176, 1.63321513679085],
                    [-1.01219701145133, 0.38050637711237, 0.67474094222355, 1.26791145841993],
                    [-0.51914611424652, 0.14189705460416, 0.27829965900511, 0.85196632717327],
                ],
                '{-1.5; -0.25; 1.25; 3.5}',
                '{-2, 0.5, 1, 4}',
            ],
            'Two row vectors' => [
                [[-2.21429743558818, 2.03444393579570, 0.67474094222355, 0.85196632717327]],
                '{-1.5, -0.25, 1.25, 3.5}',
                '{-2, 0.5, 1, 4}',
            ],
            'Two column vectors' => [
                [[-2.21429743558818], [2.03444393579570], [0.67474094222355], [0.85196632717327]],
                '{-1.5; -0.25; 1.25; 3.5}',
                '{-2; 0.5; 1; 4}',
            ],
        ];
    }
}
