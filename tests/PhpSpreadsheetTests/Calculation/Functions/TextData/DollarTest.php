<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DollarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDOLLAR
     *
     * @param mixed $expectedResult
     * @param mixed $amount
     * @param mixed $decimals
     */
    public function testDOLLAR($expectedResult, $amount = 'omitted', $decimals = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($amount === 'omitted') {
            $sheet->getCell('B1')->setValue('=DOLLAR()');
        } elseif ($decimals === 'omitted') {
            $this->setCell('A1', $amount);
            $sheet->getCell('B1')->setValue('=DOLLAR(A1)');
        } else {
            $this->setCell('A1', $amount);
            $this->setCell('A2', $decimals);
            $sheet->getCell('B1')->setValue('=DOLLAR(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDOLLAR(): array
    {
        return require 'tests/data/Calculation/TextData/DOLLAR.php';
    }

    /**
     * @dataProvider providerDollarArray
     */
    public function testDollarArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DOLLAR({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDollarArray(): array
    {
        return [
            'row vector #1' => [[['-$123.32', '$123.46', '$12,345.68']], '{-123.321, 123.456, 12345.6789}', '2'],
            'column vector #1' => [[['-$123.32'], ['$123.46'], ['$12,345.68']], '{-123.321; 123.456; 12345.6789}', '2'],
            'matrix #1' => [[['-$123.46', '$12,345.68'], ['-$123.456', '$12,345.679']], '{-123.456, 12345.6789}', '{2; 3}'],
        ];
    }
}
