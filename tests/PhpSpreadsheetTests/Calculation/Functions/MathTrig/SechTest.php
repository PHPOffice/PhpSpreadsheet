<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SechTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSECH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testSECH($expectedResult, $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=SECH($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerSECH(): array
    {
        return require 'tests/data/Calculation/MathTrig/SECH.php';
    }

    /**
     * @dataProvider providerSechArray
     */
    public function testSechArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SECH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSechArray(): array
    {
        return [
            'row vector' => [[[0.64805427366389, 0.88681888397007, 0.64805427366389]], '{1, 0.5, -1}'],
            'column vector' => [[[0.64805427366389], [0.88681888397007], [0.64805427366389]], '{1; 0.5; -1}'],
            'matrix' => [[[0.64805427366389, 0.88681888397007], [1.0, 0.64805427366389]], '{1, 0.5; 0, -1}'],
        ];
    }
}
