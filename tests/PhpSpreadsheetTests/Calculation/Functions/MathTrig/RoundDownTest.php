<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class RoundDownTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRoundDown
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testRoundDown($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=ROUNDDOWN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerRoundDown(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROUNDDOWN.php';
    }

    /**
     * @dataProvider providerRoundDownArray
     */
    public function testRoundDownArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ROUNDDOWN({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerRoundDownArray(): array
    {
        return [
            'first argument row vector' => [
                [[0.145, 1.372, -931.682, 3.141]],
                '{0.14527, 1.3725, -931.6829, 3.14159265}',
                '3',
            ],
            'first argument column vector' => [
                [[0.145], [1.372], [-931.682], [3.141]],
                '{0.14527; 1.3725; -931.6829; 3.14159265}',
                '3',
            ],
            'first argument matrix' => [
                [[0.145, 1.372], [-931.682, 3.141]],
                '{0.14527, 1.3725; -931.6829, 3.14159265}',
                '3',
            ],
            'second argument row vector' => [
                [[0.1, 0.14, 0.145, 0.1452, 0.14527]],
                '0.14527',
                '{1, 2, 3, 4, 5}',
            ],
            'second argument column vector' => [
                [[0.1], [0.14], [0.145], [0.1452], [0.14527]],
                '0.14527',
                '{1; 2; 3; 4; 5}',
            ],
            'second argument matrix' => [
                [[0.1, 0.14], [0.145, 0.1452]],
                '0.14527',
                '{1, 2; 3, 4}',
            ],
            'A row and a column vector' => [
                [
                    [0.1, 0.14, 0.145, 0.1452],
                    [1.3, 1.37, 1.372, 1.3725],
                    [-931.6, -931.68, -931.682, -931.6829],
                    [3.1, 3.14, 3.141, 3.1415],
                ],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two row vectors' => [
                [[0.1, 1.37, -931.682, 3.1415]],
                '{0.14527, 1.37250, -931.68290, 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two column vectors' => [
                [[0.1], [1.37], [-931.682], [3.1415]],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1; 2; 3; 4}',
            ],
        ];
    }
}
