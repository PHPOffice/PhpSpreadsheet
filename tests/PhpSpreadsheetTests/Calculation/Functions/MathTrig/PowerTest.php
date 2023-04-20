<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class PowerTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPOWER
     *
     * @param mixed $expectedResult
     * @param mixed $base
     * @param mixed $exponent
     */
    public function testPOWER($expectedResult, $base = 'omitted', $exponent = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($base !== null) {
            $sheet->getCell('A1')->setValue($base);
        }
        if ($exponent !== null) {
            $sheet->getCell('A2')->setValue($exponent);
        }
        if ($base === 'omitted') {
            $sheet->getCell('B1')->setValue('=POWER()');
        } elseif ($exponent === 'omitted') {
            $sheet->getCell('B1')->setValue('=POWER(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=POWER(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerPOWER(): array
    {
        return require 'tests/data/Calculation/MathTrig/POWER.php';
    }

    /**
     * @dataProvider providerPowerArray
     */
    public function testPowerArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=POWER({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerPowerArray(): array
    {
        return [
            'matrix' => [[[729, 512, 343], [216, 125, 64], [27, 8, 1]], '{9, 8, 7; 6, 5, 4; 3, 2, 1}', '3'],
        ];
    }
}
