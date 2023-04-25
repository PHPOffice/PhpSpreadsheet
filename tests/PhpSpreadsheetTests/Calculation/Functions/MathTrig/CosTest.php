<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CosTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCos
     *
     * @param mixed $expectedResult
     */
    public function testCos($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=COS($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerCos(): array
    {
        return require 'tests/data/Calculation/MathTrig/COS.php';
    }

    /**
     * @dataProvider providerCosArray
     */
    public function testCosArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COS({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCosArray(): array
    {
        return [
            'row vector' => [[[0.54030230586814, 0.87758256189037, 0.54030230586814]], '{1, 0.5, -1}'],
            'column vector' => [[[0.54030230586814], [0.87758256189037], [0.54030230586814]], '{1; 0.5; -1}'],
            'matrix' => [[[0.54030230586814, 0.87758256189037], [1.0, 0.54030230586814]], '{1, 0.5; 0, -1}'],
        ];
    }
}
