<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AcothTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACOTH
     */
    public function testACOTH(float|int|string $expectedResult, float|int|string $number): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -10);
        $sheet->getCell('A1')->setValue("=ACOTH($number)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerACOTH(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOTH.php';
    }

    /**
     * @dataProvider providerAcothArray
     */
    public function testAcothArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ACOTH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAcothArray(): array
    {
        return [
            'row vector' => [[[-0.20273255405408, 0.54930614433406, 0.13413199329734]], '{-5, 2, 7.5}'],
            'column vector' => [[[-0.20273255405408], [0.54930614433406], [0.13413199329734]], '{-5; 2; 7.5}'],
            'matrix' => [[[-0.20273255405408, 0.54930614433406], ['#NUM!', 0.13413199329734]], '{-5, 2; 0, 7.5}'],
        ];
    }
}
