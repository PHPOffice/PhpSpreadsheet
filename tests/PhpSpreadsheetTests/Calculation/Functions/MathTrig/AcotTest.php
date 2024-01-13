<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AcotTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACOT
     */
    public function testACOT(float|int|string $expectedResult, float|int|string $number): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5);
        $sheet->getCell('A1')->setValue("=ACOT($number)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerACOT(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOT.php';
    }

    /**
     * @dataProvider providerAcotArray
     */
    public function testAcotArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ACOT({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAcotArray(): array
    {
        return [
            'row vector' => [[[0.78539816339745, 1.10714871779409, 2.35619449019234]], '{1, 0.5, -1}'],
            'column vector' => [[[0.78539816339745], [1.10714871779409], [2.35619449019234]], '{1; 0.5; -1}'],
            'matrix' => [[[0.78539816339745, 1.10714871779409], [1.57079632679490, 2.35619449019234]], '{1, 0.5; 0, -1}'],
        ];
    }
}
