<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AtanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAtan
     *
     * @param mixed $expectedResult
     */
    public function testAtan($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A1')->setValue("=ATAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAtan(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATAN.php';
    }

    /**
     * @dataProvider providerAtanArray
     */
    public function testAtanArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ATAN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAtanArray(): array
    {
        return [
            'row vector' => [[[0.78539816339745, 0.46364760900081, -0.78539816339745]], '{1, 0.5, -1}'],
            'column vector' => [[[0.78539816339745], [0.46364760900081], [-0.78539816339745]], '{1; 0.5; -1}'],
            'matrix' => [[[0.78539816339745, 0.46364760900081], [0.0, -0.78539816339745]], '{1, 0.5; 0, -1}'],
        ];
    }
}
