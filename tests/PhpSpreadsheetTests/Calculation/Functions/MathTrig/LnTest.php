<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LnTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLN
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testLN($expectedResult, $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=LN()');
        } else {
            $sheet->getCell('B1')->setValue('=LN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerLN(): array
    {
        return require 'tests/data/Calculation/MathTrig/LN.php';
    }

    /**
     * @dataProvider providerLnArray
     */
    public function testLnArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLnArray(): array
    {
        return [
            'row vector' => [[[-2.07944154167984, 0.85228540189824, 2.525728644308256]], '{0.125, 2.345, 12.5}'],
            'column vector' => [[[-2.07944154167984], [0.85228540189824], [2.525728644308256]], '{0.125; 2.345; 12.5}'],
            'matrix' => [[[-2.07944154167984, 0.85228540189824], [0.0, 2.525728644308256]], '{0.125, 2.345; 1.0, 12.5}'],
        ];
    }
}
