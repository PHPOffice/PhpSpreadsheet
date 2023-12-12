<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LogTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOG
     */
    public function testLOG(mixed $expectedResult, mixed $number = 'omitted', mixed $base = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($base !== null) {
            $sheet->getCell('A2')->setValue($base);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG()');
        } elseif ($base === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=LOG(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerLOG(): array
    {
        return require 'tests/data/Calculation/MathTrig/LOG.php';
    }

    /**
     * @dataProvider providerLogArray
     */
    public function testLogArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOG({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLogArray(): array
    {
        return [
            'matrix' => [
                [
                    [-0.90308998699194, 0.3701428470511, 0.0, 1.09691001300806],
                    [-2.07944154167984, 0.85228540189824, 0.0, 2.525728644308256],
                ],
                '{0.125, 2.345, 1.0, 12.5}',
                '{10; 2.718281828459045}',
            ],
        ];
    }
}
