<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class YearFracTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYEARFRAC
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    public function testYEARFRAC($expectedResult, $arg1 = 'omitted', $arg2 = 'omitted', $arg3 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        if ($arg3 !== null) {
            $sheet->getCell('A3')->setValue($arg3);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=YEARFRAC()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=YEARFRAC(A1)');
        } elseif ($arg3 === 'omitted') {
            $sheet->getCell('B1')->setValue('=YEARFRAC(A1, A2)');
        } else {
            $sheet->getCell('B1')->setValue('=YEARFRAC(A1, A2, A3)');
        }
        self::assertEqualswithDelta($expectedResult, $sheet->getCell('B1')->getCalculatedValue(), 1E-6);
    }

    public function providerYEARFRAC(): array
    {
        return require 'tests/data/Calculation/DateTime/YEARFRAC.php';
    }

    /**
     * @dataProvider providerYearFracArray
     */
    public function testYearFracArray(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=YEARFRAC({$startDate}, {$endDate})";
        } else {
            $formula = "=YEARFRAC({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerYearFracArray(): array
    {
        return [
            'row vector #1' => [[[1.0, 0.55277777777778, 0.56111111111111]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'column vector #1' => [[[1.0], [0.99444444444445], [0.98611111111111]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[0.002777777777778, 0.027777777777778], [0.625, 1.0]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"', null],
            'column vector with methods' => [[[0.99726027397260, 0.99722222222222], [0.99178082191781, 0.99166666666667], [0.98356164383562, 0.98333333333333]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', '{1, 4}'],
        ];
    }
}
