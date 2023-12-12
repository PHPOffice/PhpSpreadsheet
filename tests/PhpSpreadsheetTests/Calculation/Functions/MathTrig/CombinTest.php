<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CombinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOMBIN
     */
    public function testCOMBIN(mixed $expectedResult, mixed $numObjs, mixed $numInSet): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($numObjs !== null) {
            $sheet->getCell('A1')->setValue($numObjs);
        }
        if ($numInSet !== null) {
            $sheet->getCell('A2')->setValue($numInSet);
        }
        $sheet->getCell('B1')->setValue('=COMBIN(A1,A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCOMBIN(): array
    {
        return require 'tests/data/Calculation/MathTrig/COMBIN.php';
    }

    /**
     * @dataProvider providerCombinArray
     */
    public function testCombinArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMBIN({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCombinArray(): array
    {
        return [
            'first argument row vector' => [
                [[56, 10]],
                '{8, 5}',
                '3',
            ],
            'first argument column vector' => [
                [[56], [10]],
                '{8; 5}',
                '3',
            ],
            'first argument matrix' => [
                [[56, 10], [1, 35]],
                '{8, 5; 3, 7}',
                '3',
            ],
            'second argument row vector' => [
                [[286, 1716]],
                '13',
                '{3, 6}',
            ],
            'second argument column vector' => [
                [[286], [1716]],
                '13',
                '{3; 6}',
            ],
            'second argument matrix' => [
                [[286, 1716], [715, 1287]],
                '13',
                '{3, 6; 4, 8}',
            ],
            'A row and a column vector' => [
                [
                    [792, 495, 220, 66],
                    [252, 210, 120, 45],
                    [56, 70, 56, 28],
                    [6, 15, 20, 15],
                ],
                '{12; 10; 8; 6}',
                '{5, 4, 3, 2}',
            ],
            'Two row vectors' => [
                [[792, 210, 56, 15]],
                '{12, 10, 8, 6}',
                '{5, 4, 3, 2}',
            ],
            'Two column vectors' => [
                [[792], [210], [56], [15]],
                '{12; 10; 8; 6}',
                '{5; 4; 3; 2}',
            ],
        ];
    }
}
