<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class IntTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINT
     */
    public function testINT(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=INT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerINT(): array
    {
        return require 'tests/data/Calculation/MathTrig/INT.php';
    }

    /**
     * @dataProvider providerIntArray
     */
    public function testIntArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=INT({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerIntArray(): array
    {
        return [
            'row vector' => [[[-2, 0, 0]], '{-1.5, 0, 0.3}'],
            'column vector' => [[[-2], [0], [0]], '{-1.5; 0; 0.3}'],
            'matrix' => [[[-2, 0], [0, 12]], '{-1.5, 0; 0.3, 12.5}'],
        ];
    }
}
