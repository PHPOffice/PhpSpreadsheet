<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class RoundUpTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRoundUp
     */
    public function testRoundUp(float|int|string $expectedResult, float|int|string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=ROUNDUP($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerRoundUp(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROUNDUP.php';
    }

    /**
     * @dataProvider providerRoundUpArray
     */
    public function testRoundUpArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ROUNDUP({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerRoundUpArray(): array
    {
        return [
            'first argument row vector' => [
                [[0.146, 1.373, -931.683, 3.142]],
                '{0.14527, 1.3725, -931.6829, 3.14159265}',
                '3',
            ],
            'first argument column vector' => [
                [[0.146], [1.373], [-931.683], [3.142]],
                '{0.14527; 1.3725; -931.6829; 3.14159265}',
                '3',
            ],
            'first argument matrix' => [
                [[0.146, 1.373], [-931.683, 3.142]],
                '{0.14527, 1.3725; -931.6829, 3.14159265}',
                '3',
            ],
            'second argument row vector' => [
                [[0.2, 0.15, 0.146, 0.1453, 0.14527]],
                '0.14527',
                '{1, 2, 3, 4, 5}',
            ],
            'second argument column vector' => [
                [[0.2], [0.15], [0.146], [0.1453], [0.14527]],
                '0.14527',
                '{1; 2; 3; 4; 5}',
            ],
            'second argument matrix' => [
                [[0.2, 0.15], [0.146, 0.1453]],
                '0.14527',
                '{1, 2; 3, 4}',
            ],
            'A row and a column vector' => [
                [
                    [0.2, 0.15, 0.146, 0.1453],
                    [1.4, 1.38, 1.373, 1.3725],
                    [-931.7, -931.69, -931.683, -931.6829],
                    [3.2, 3.15, 3.142, 3.1416],
                ],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two row vectors' => [
                [[0.2, 1.38, -931.683, 3.1416]],
                '{0.14527, 1.37250, -931.68290, 3.14159265}',
                '{1, 2, 3, 4}',
            ],
            'Two column vectors' => [
                [[0.2], [1.38], [-931.683], [3.1416]],
                '{0.14527; 1.37250; -931.68290; 3.14159265}',
                '{1; 2; 3; 4}',
            ],
        ];
    }
}
