<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CothTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOTH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCOTH($expectedResult, $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=COTH($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerCOTH(): array
    {
        return require 'tests/data/Calculation/MathTrig/COTH.php';
    }

    /**
     * @dataProvider providerCothArray
     */
    public function testCothArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COTH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCothArray(): array
    {
        return [
            'row vector' => [[[1.31303528549933, 2.16395341373865, -1.31303528549933]], '{1, 0.5, -1}'],
            'column vector' => [[[1.31303528549933], [2.16395341373865], [-1.31303528549933]], '{1; 0.5; -1}'],
            'matrix' => [[[1.31303528549933, 2.16395341373865], ['#DIV/0!', -1.31303528549933]], '{1, 0.5; 0, -1}'],
        ];
    }
}
