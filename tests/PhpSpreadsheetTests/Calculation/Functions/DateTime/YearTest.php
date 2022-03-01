<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class YearTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYEAR
     *
     * @param mixed $expectedResult
     */
    public function testYEAR($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=YEAR($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerYEAR(): array
    {
        return require 'tests/data/Calculation/DateTime/YEAR.php';
    }

    /**
     * @dataProvider providerYearArray
     */
    public function testYearArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=YEAR({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerYearArray(): array
    {
        return [
            'row vector' => [[[2021, 2022, 2023]], '{"2021-01-01", "2022-01-01", "2023-01-01"}'],
            'column vector' => [[[2021], [2022], [2023]], '{"2021-01-01"; "2022-01-01"; "2023-01-01"}'],
            'matrix' => [[[2021, 2022], [2023, 1999]], '{"2021-01-01", "2022-01-01"; "2023-01-01", "1999-12-31"}'],
        ];
    }
}
