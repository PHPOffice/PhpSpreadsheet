<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSin
     *
     * @param mixed $expectedResult
     */
    public function testSin($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=SIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerSin(): array
    {
        return require 'tests/data/Calculation/MathTrig/SIN.php';
    }

    /**
     * @dataProvider providerSinArray
     */
    public function testSinArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SIN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSinArray(): array
    {
        return [
            'row vector' => [[[0.84147098480790, 0.47942553860420, -0.84147098480790]], '{1, 0.5, -1}'],
            'column vector' => [[[0.84147098480790], [0.47942553860420], [-0.84147098480790]], '{1; 0.5; -1}'],
            'matrix' => [[[0.84147098480790, 0.47942553860420], [0.0, -0.84147098480790]], '{1, 0.5; 0, -1}'],
        ];
    }
}
