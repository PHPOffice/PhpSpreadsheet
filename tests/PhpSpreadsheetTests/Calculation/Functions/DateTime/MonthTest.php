<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class MonthTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMONTH
     *
     * @param mixed $expectedResult
     */
    public function testMONTH($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=MONTH($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerMONTH(): array
    {
        return require 'tests/data/Calculation/DateTime/MONTH.php';
    }

    /**
     * @dataProvider providerMonthArray
     */
    public function testMonthArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MONTH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerMonthArray(): array
    {
        return [
            'row vector' => [[[1, 6, 1]], '{"2022-01-01", "2022-06-01", "2023-01-01"}'],
            'column vector' => [[[1], [3], [6]], '{"2022-01-01"; "2022-03-01"; "2022-06-01"}'],
            'matrix' => [[[1, 4], [8, 12]], '{"2022-01-01", "2022-04-01"; "2022-08-01", "2022-12-01"}'],
        ];
    }
}
