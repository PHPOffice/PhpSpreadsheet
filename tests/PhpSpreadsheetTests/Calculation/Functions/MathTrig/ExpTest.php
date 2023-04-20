<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ExpTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEXP
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testEXP($expectedResult, $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=EXP()');
        } else {
            $sheet->getCell('B1')->setValue('=EXP(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerEXP(): array
    {
        return require 'tests/data/Calculation/MathTrig/EXP.php';
    }

    /**
     * @dataProvider providerExpArray
     */
    public function testExpArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EXP({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerExpArray(): array
    {
        return [
            'row vector' => [[[1.0, 2.718281828459045, 12.182493960703473]], '{0, 1, 2.5}'],
            'column vector' => [[[1.0], [2.718281828459045], [12.182493960703473]], '{0; 1; 2.5}'],
            'matrix' => [[[1.0, 2.718281828459045], [12.182493960703473, 0.0820849986239]], '{0, 1; 2.5, -2.5}'],
        ];
    }
}
