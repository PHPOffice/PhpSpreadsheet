<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class EvenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testEVEN($expectedResult, $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=EVEN($value)");
        $sheet->getCell('A2')->setValue(3.7);
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerEVEN(): array
    {
        return require 'tests/data/Calculation/MathTrig/EVEN.php';
    }

    /**
     * @dataProvider providerEvenArray
     */
    public function testEvenArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EVEN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerEvenArray(): array
    {
        return [
            'row vector' => [[[-4, 2, 4]], '{-3, 1, 4}'],
            'column vector' => [[[-4], [2], [4]], '{-3; 1; 4}'],
            'matrix' => [[[-4, 2], [4, 2]], '{-3, 1; 4, 1.5}'],
        ];
    }
}
