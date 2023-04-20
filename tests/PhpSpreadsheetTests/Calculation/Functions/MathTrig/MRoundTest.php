<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class MRoundTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMROUND
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testMROUND($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=MROUND($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerMROUND(): array
    {
        return require 'tests/data/Calculation/MathTrig/MROUND.php';
    }

    /**
     * @dataProvider providerMRoundArray
     */
    public function testMRoundArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MROUND({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerMRoundArray(): array
    {
        return [
            'first argument row vector' => [
                [[0.15, 1.375, 931.675, 3.15]],
                '{0.14527, 1.3725, 931.6829, 3.14159265}',
                '0.025',
            ],
            'first argument column vector' => [
                [[0.15], [1.375], [931.675], [3.15]],
                '{0.14527; 1.3725; 931.6829; 3.14159265}',
                '0.025',
            ],
            'first argument matrix' => [
                [[0.15, 1.375], [931.675, 3.15]],
                '{0.14527, 1.3725; 931.6829, 3.14159265}',
                '0.025',
            ],
            'second argument row vector' => [
                [[123.125, 123.125, 123.1, 123.2, 125]],
                '123.12345',
                '{0.005, 0.025, 0.1, 0.2, 5}',
            ],
            'second argument column vector' => [
                [[123.125], [123.125], [123.1], [123.2], [125]],
                '123.12345',
                '{0.005; 0.025; 0.1; 0.2; 5}',
            ],
            'second argument matrix' => [
                [[123.125, 123.125], [123.2, 125.0]],
                '123.12345',
                '{0.005, 0.025; 0.2, 5}',
            ],
            'A row and a column vector' => [
                [
                    [0.145, 0.15, 0.2, 0.0],
                    [1.375, 1.375, 1.4, 0.0],
                    [931.685, 931.675, 931.6, 930.0],
                    [3.14, 3.15, 3.2, 5.0],
                ],
                '{0.14527; 1.37250; 931.68290; 3.14159265}',
                '{0.005, 0.025, 0.2, 5}',
            ],
            'Two row vectors' => [
                [[0.145, 1.375, 931.6, 5.0]],
                '{0.14527, 1.3725, 931.6, 3.14159265}',
                '{0.005, 0.025, 0.2, 5}',
            ],
            'Two column vectors' => [
                [[0.145], [1.375], [931.6], [5.0]],
                '{0.14527; 1.37250; 931.6; 5}',
                '{0.005; 0.025; 0.2; 5}',
            ],
        ];
    }
}
