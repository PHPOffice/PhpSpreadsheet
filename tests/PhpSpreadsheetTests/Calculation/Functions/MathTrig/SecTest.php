<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SecTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSEC
     */
    public function testSEC(float|int|string $expectedResult, float|int|string $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=SEC($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerSEC(): array
    {
        return require 'tests/data/Calculation/MathTrig/SEC.php';
    }

    /**
     * @dataProvider providerSecArray
     */
    public function testSecArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SEC({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSecArray(): array
    {
        return [
            'row vector' => [[[1.85081571768093, 1.13949392732455, 1.85081571768093]], '{1, 0.5, -1}'],
            'column vector' => [[[1.85081571768093], [1.13949392732455], [1.85081571768093]], '{1; 0.5; -1}'],
            'matrix' => [[[1.85081571768093, 1.13949392732455], [1.0, 1.85081571768093]], '{1, 0.5; 0, -1}'],
        ];
    }
}
