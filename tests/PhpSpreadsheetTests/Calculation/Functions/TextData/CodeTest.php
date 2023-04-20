<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CodeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCODE
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testCODE($expectedResult, $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CODE()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CODE(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCODE(): array
    {
        return require 'tests/data/Calculation/TextData/CODE.php';
    }

    /**
     * @dataProvider providerCodeArray
     */
    public function testCodeArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CODE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCodeArray(): array
    {
        return [
            'row vector' => [[[80, 72, 80]], '{"P", "H", "P"}'],
            'column vector' => [[[80], [72], [80]], '{"P"; "H"; "P"}'],
            'matrix' => [[[89, 111], [108, 111]], '{"Y", "o"; "l", "o"}'],
        ];
    }
}
