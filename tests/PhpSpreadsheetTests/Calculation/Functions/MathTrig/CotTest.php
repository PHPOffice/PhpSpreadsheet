<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CotTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOT
     */
    public function testCOT(float|int|string $expectedResult, float|int|string $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=COT($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerCOT(): array
    {
        return require 'tests/data/Calculation/MathTrig/COT.php';
    }

    /**
     * @dataProvider providerCotArray
     */
    public function testCotArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COT({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCotArray(): array
    {
        return [
            'row vector' => [[[0.64209261593433, 1.83048772171245, -0.64209261593433]], '{1, 0.5, -1}'],
            'column vector' => [[[0.64209261593433], [1.83048772171245], [-0.64209261593433]], '{1; 0.5; -1}'],
            'matrix' => [[[0.64209261593433, 1.83048772171245], ['#DIV/0!', -0.64209261593433]], '{1, 0.5; 0, -1}'],
        ];
    }
}
