<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class RoundTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRound
     */
    public function testRound(float|int|string $expectedResult, float|int|string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=ROUND($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerRound(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROUND.php';
    }

    /**
     * @dataProvider providerRoundArray
     */
    public function testRoundArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ROUND({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerRoundArray(): array
    {
        return [
            'first argument row vector' => [
                [[0.145, 1.373, -931.683, 3.142]],
                '{0.14527, 1.3725, -931.6829, 3.14159265}',
                '3',
            ],
            'first argument column vector' => [
                [[0.145], [1.373], [-931.683], [3.142]],
                '{0.14527; 1.3725; -931.6829; 3.14159265}',
                '3',
            ],
            'first argument matrix' => [
                [[0.145, 1.373], [-931.683, 3.142]],
                '{0.14527, 1.3725; -931.6829, 3.14159265}',
                '3',
            ],
            'second argument row vector' => [
                [[0.1, 0.12, 0.123, 0.1235, 0.12345]],
                '0.12345',
                '{1, 2, 3, 4, 5}',
            ],
            'second argument column vector' => [
                [[0.1], [0.12], [0.123], [0.1235], [0.12345]],
                '0.12345',
                '{1; 2; 3; 4; 5}',
            ],
            'second argument matrix' => [
                [[0.1, 0.12], [0.123, 0.1235]],
                '0.12345',
                '{1, 2; 3, 4}',
            ],
            'A row and a column vector' => [
                [
                    [0.1, 0.15, 0.145, 0.1453],
                    [1.4, 1.37, 1.373, 1.3725],
                    [-931.7, -931.68, -931.683, -931.6829],
                    [3.1, 3.14, 3.142, 3.1416],
                ],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two row vectors' => [
                [[0.1, 1.37, -931.683, 3.1416]],
                '{0.14527, 1.37250, -931.68290, 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two different size row vectors' => [
                [[0.1, 1.37]],
                '{0.14527, 1.37250, -931.68290, 3.14159265}',
                '{1, 2}',
            ],
            'Two column vectors' => [
                [[0.1], [1.37], [-931.683], [3.1416]],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1; 2; 3; 4}',
            ],
            'Two matching square matrices' => [
                [
                    [10000, 46000, 78900],
                    [23460, 56789, 89012.3],
                    [34567.89, 67890.123, 90123.4568],
                ],
                '{12345.67890, 45678.90123, 78901.23456; 23456.78901, 56789.01234, 89012.34567; 34567.89012, 67890.12345, 90123.45678}',
                '{-4, -3, -2; -1, 0, 1; 2, 3, 4}',
            ],
            'Two paired 2x3 matrices' => [
                [
                    [10000, 46000, 78900],
                    [23460, 56789, 89012.3],
                ],
                '{12345.67890, 45678.90123, 78901.23456; 23456.78901, 56789.01234, 89012.34567}',
                '{-4, -3, -2; -1, 0, 1}',
            ],
            'Two paired 3x2 matrices' => [
                [
                    [10000, 46000],
                    [23460, 56789],
                    [34567.89, 67890.123],
                ],
                '{12345.67890, 45678.90123; 23456.78901, 56789.01234; 34567.89012, 67890.12345}',
                '{-4, -3; -1, 0; 2, 3}',
            ],
            'Two mismatched matrices (2x3 and 2x2)' => [
                [
                    [10000, 46000],
                    [23460, 56789],
                ],
                '{12345.67890, 45678.90123, 78901.23456; 23456.78901, 56789.01234, 89012.34567}',
                '{-4, -3; -1, 0}',
            ],
            'Two mismatched matrices (3x2 and 2x1 vector)' => [
                [
                    [10000, 50000],
                    [23460, 56790],
                ],
                '{12345.67890, 45678.90123; 23456.78901, 56789.01234; 34567.89012, 67890.12345}',
                '{-4; -1}',
            ],
            'Two mismatched matrices (3x1 vector and 2x2)' => [
                [
                    [10000, 12000],
                    [23460, 23457],
                ],
                '{12345.67890; 23456.78901; 34567.89012}',
                '{-4, -3; -1, 0}',
            ],
            'Two mismatched matrices (1x3 vector and 2x2)' => [
                [
                    [10000, 46000],
                    [12350, 45679],
                ],
                '{12345.67890, 45678.90123, 78901.23456}',
                '{-4, -3; -1, 0}',
            ],
            'Larger mismatched matrices (5x1 vector and 3x3)' => [
                [
                    [3.1, 6.28, 9.425],
                    [12.6, 15.71, 18.85],
                    [22, 25.13, 28.274],
                ],
                '{3.14159265, 6.2831853, 9.424777959; 12.5663706, 15.70796325, 18.8495559; 21.99114855, 25.1327412, 28.27433385}',
                '{1, 2, 3, 4, 5}',
            ],
            'Two different sized column vectors' => [
                [[0.1], [1.37]],
                '{0.14527; 1.37250}',
                '{1; 2; 3; 4}',
            ],
        ];
    }
}
