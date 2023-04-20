<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CombinATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOMBINA
     *
     * @param mixed $expectedResult
     * @param mixed $numObjs
     * @param mixed $numInSet
     */
    public function testCOMBINA($expectedResult, $numObjs, $numInSet): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($numObjs !== null) {
            $sheet->getCell('A1')->setValue($numObjs);
        }
        if ($numInSet !== null) {
            $sheet->getCell('A2')->setValue($numInSet);
        }
        $sheet->getCell('B1')->setValue('=COMBINA(A1,A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCOMBINA(): array
    {
        return require 'tests/data/Calculation/MathTrig/COMBINA.php';
    }

    /**
     * @dataProvider providerCombinAArray
     */
    public function testCombinAArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMBINA({$argument1},{$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCombinAArray(): array
    {
        return [
            'first argument row vector' => [
                [[120, 35]],
                '{8, 5}',
                '3',
            ],
            'first argument column vector' => [
                [[120], [35]],
                '{8; 5}',
                '3',
            ],
            'first argument matrix' => [
                [[120, 35], [10, 84]],
                '{8, 5; 3, 7}',
                '3',
            ],
            'second argument row vector' => [
                [[455, 18564]],
                '13',
                '{3, 6}',
            ],
            'second argument column vector' => [
                [[455], [18564]],
                '13',
                '{3; 6}',
            ],
            'second argument matrix' => [
                [[455, 18564], [1820, 125970]],
                '13',
                '{3, 6; 4, 8}',
            ],
            'A row and a column vector' => [
                [
                    [4368, 1365, 364, 78],
                    [2002, 715, 220, 55],
                    [792, 330, 120, 36],
                    [252, 126, 56, 21],
                ],
                '{12; 10; 8; 6}',
                '{5, 4, 3, 2}',
            ],
            'Two row vectors' => [
                [[4368, 715, 120, 21]],
                '{12, 10, 8, 6}',
                '{5, 4, 3, 2}',
            ],
            'Two column vectors' => [
                [[4368], [715], [120], [21]],
                '{12; 10; 8; 6}',
                '{5; 4; 3; 2}',
            ],
        ];
    }
}
