<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AsinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAsin
     *
     * @param mixed $expectedResult
     */
    public function testAsin($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ASIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAsin(): array
    {
        return require 'tests/data/Calculation/MathTrig/ASIN.php';
    }

    /**
     * @dataProvider providerAsinArray
     */
    public function testAsinArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ASIN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAsinArray(): array
    {
        return [
            'row vector' => [[[1.57079632679490, 0.52359877559830, -1.57079632679490]], '{1, 0.5, -1}'],
            'column vector' => [[[1.57079632679490], [0.52359877559830], [-1.57079632679490]], '{1; 0.5; -1}'],
            'matrix' => [[[1.57079632679490, 0.52359877559830], [0.0, -1.57079632679490]], '{1, 0.5; 0, -1}'],
        ];
    }
}
