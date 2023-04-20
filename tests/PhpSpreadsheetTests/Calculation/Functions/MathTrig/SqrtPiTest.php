<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SqrtPiTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testSQRTPI($expectedResult, $number): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=SQRTPI()');
        } else {
            $sheet->getCell('B1')->setValue('=SQRTPI(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSQRTPI(): array
    {
        return require 'tests/data/Calculation/MathTrig/SQRTPI.php';
    }

    /**
     * @dataProvider providerSqrtPiArray
     */
    public function testSqrtPiArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SQRTPI({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerSqrtPiArray(): array
    {
        return [
            'row vector' => [[[5.317361552716, 6.2665706865775, 8.6832150546992]], '{9, 12.5, 24}'],
            'column vector' => [[[5.3173615527166], [6.2665706865775], [8.6832150546992]], '{9; 12.5; 24}'],
            'matrix' => [[[5.3173615527166, 6.2665706865775], [8.6832150546992, 14.1796308072441]], '{9, 12.5; 24, 64}'],
        ];
    }
}
