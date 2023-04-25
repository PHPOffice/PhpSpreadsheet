<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CeilingMathTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCEILINGMATH
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testCEILINGMATH($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CEILING.MATH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerCEILINGMATH(): array
    {
        return require 'tests/data/Calculation/MathTrig/CEILINGMATH.php';
    }

    /**
     * @dataProvider providerCeilingArray
     */
    public function testCeilingArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CEILING.MATH({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCeilingArray(): array
    {
        return [
            'matrix' => [[[3.15, 3.142], [3.1416, 3.141594]], '3.1415926536', '{0.01, 0.002; 0.00005, 0.000002}'],
        ];
    }
}
