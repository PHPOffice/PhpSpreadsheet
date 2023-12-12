<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AsinhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAsinh
     */
    public function testAsinh(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ASINH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAsinh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ASINH.php';
    }

    /**
     * @dataProvider providerAsinhArray
     */
    public function testAsinhArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ASINH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAsinhArray(): array
    {
        return [
            'row vector' => [[[0.88137358701954, 0.48121182505960, -0.88137358701954]], '{1, 0.5, -1}'],
            'column vector' => [[[0.88137358701954], [0.48121182505960], [-0.88137358701954]], '{1; 0.5; -1}'],
            'matrix' => [[[0.88137358701954, 0.48121182505960], [0.0, -0.88137358701954]], '{1, 0.5; 0, -1}'],
        ];
    }
}
