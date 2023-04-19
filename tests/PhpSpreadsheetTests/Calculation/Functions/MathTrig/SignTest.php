<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SignTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSIGN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testSIGN($expectedResult, $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 0);
        $sheet->setCellValue('A4', -3.8);
        $sheet->getCell('A1')->setValue("=SIGN($value)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerSIGN(): array
    {
        return require 'tests/data/Calculation/MathTrig/SIGN.php';
    }

    /**
     * @dataProvider providerSignArray
     */
    public function testSignArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SIGN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSignArray(): array
    {
        return [
            'row vector' => [[[-1, 0, 1]], '{-1.5, 0, 0.3}'],
            'column vector' => [[[-1], [0], [1]], '{-1.5; 0; 0.3}'],
            'matrix' => [[[-1, 0], [1, 1]], '{-1.5, 0; 0.3, 12.5}'],
        ];
    }
}
