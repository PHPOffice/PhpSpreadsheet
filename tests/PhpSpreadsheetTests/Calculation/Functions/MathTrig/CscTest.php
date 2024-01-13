<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCSC
     */
    public function testCSC(float|int|string $expectedResult, float|int|string $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CSC($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerCSC(): array
    {
        return require 'tests/data/Calculation/MathTrig/CSC.php';
    }

    /**
     * @dataProvider providerCscArray
     */
    public function testCscArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CSC({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCscArray(): array
    {
        return [
            'row vector' => [[[1.18839510577812, 2.08582964293349, -1.18839510577812]], '{1, 0.5, -1}'],
            'column vector' => [[[1.18839510577812], [2.08582964293349], [-1.18839510577812]], '{1; 0.5; -1}'],
            'matrix' => [[[1.18839510577812, 2.08582964293349], ['#DIV/0!', -1.18839510577812]], '{1, 0.5; 0, -1}'],
        ];
    }
}
