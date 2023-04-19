<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NumberValueTest extends AllSetupTeardown
{
    const NV_PRECISION = 1.0E-8;

    /**
     * @dataProvider providerNUMBERVALUE
     *
     * @param mixed $expectedResult
     * @param mixed $number
     * @param mixed $decimal
     * @param mixed $group
     */
    public function testNUMBERVALUE($expectedResult, $number = 'omitted', $decimal = 'omitted', $group = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=NUMBERVALUE()');
        } elseif ($decimal === 'omitted') {
            $this->setCell('A1', $number);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1)');
        } elseif ($group === 'omitted') {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimal);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1, A2)');
        } else {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimal);
            $this->setCell('A3', $group);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::NV_PRECISION);
    }

    public static function providerNUMBERVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/NUMBERVALUE.php';
    }

    /**
     * @dataProvider providerNumberValueArray
     */
    public function testNumberValueArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NumberValue({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::NV_PRECISION);
    }

    public static function providerNumberValueArray(): array
    {
        return [
            'row vector #1' => [[[-123.321, 123.456, 12345.6789]], '{"-123,321", "123,456", "12 345,6789"}', '","', '" "'],
            'column vector #1' => [[[-123.321], [123.456], [12345.6789]], '{"-123,321"; "123,456"; "12 345,6789"}', '","', '" "'],
        ];
    }
}
