<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FactTest extends AllSetupTeardown
{
    const FACT_PRECISION = 1E-12;

    /**
     * @dataProvider providerFACT
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     */
    public function testFACT($expectedResult, $arg1): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=FACT()');
        } else {
            $sheet->getCell('B1')->setValue('=FACT(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerFACT(): array
    {
        return require 'tests/data/Calculation/MathTrig/FACT.php';
    }

    /**
     * @dataProvider providerFACTGnumeric
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     */
    public function testFACTGnumeric($expectedResult, $arg1): void
    {
        $this->mightHaveException($expectedResult);
        self::setGnumeric();
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=FACT()');
        } else {
            $sheet->getCell('B1')->setValue('=FACT(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::FACT_PRECISION);
    }

    public static function providerFACTGnumeric(): array
    {
        return require 'tests/data/Calculation/MathTrig/FACTGNUMERIC.php';
    }

    /**
     * @dataProvider providerFactArray
     */
    public function testFactArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FACT({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::FACT_PRECISION);
    }

    public static function providerFactArray(): array
    {
        return [
            'row vector' => [[['#NUM!', 120, 362880]], '{-2, 5, 9}'],
            'column vector' => [[['#NUM!'], [120], [362880]], '{-2; 5; 9}'],
            'matrix' => [[['#NUM!', 120], [362880, 6]], '{-2, 5; 9, 3.5}'],
        ];
    }
}
