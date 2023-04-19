<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TruncTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testTRUNC($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=TRUNC($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerTRUNC(): array
    {
        return require 'tests/data/Calculation/MathTrig/TRUNC.php';
    }

    /**
     * @dataProvider providerTruncArray
     */
    public function testTruncArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TRUNC({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTruncArray(): array
    {
        return [
            'matrix' => [[[3.14, 3.141], [3.14159, 3.14159265]], '3.1415926536', '{2, 3; 5, 8}'],
        ];
    }
}
