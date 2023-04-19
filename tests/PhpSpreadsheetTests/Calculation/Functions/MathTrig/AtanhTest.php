<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AtanhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAtanh
     *
     * @param mixed $expectedResult
     */
    public function testAtanh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.8);
        $sheet->getCell('A1')->setValue("=ATANH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAtanh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATANH.php';
    }

    /**
     * @dataProvider providerAtanhArray
     */
    public function testAtanhArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ATANH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAtanhArray(): array
    {
        return [
            'row vector' => [[[1.83178082306482, 0.54930614433406, -1.83178082306482]], '{0.95, 0.5, -0.95}'],
            'column vector' => [[[1.83178082306482], [0.54930614433406], [-1.83178082306482]], '{0.95; 0.5; -0.95}'],
            'matrix' => [[[1.83178082306482, 0.54930614433406], [0.0, -1.83178082306482]], '{0.95, 0.5; 0, -0.95}'],
        ];
    }
}
