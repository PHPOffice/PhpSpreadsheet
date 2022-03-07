<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class WeekNumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerWEEKNUM
     *
     * @param mixed $expectedResult
     */
    public function testWEEKNUM($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=WEEKNUM($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerWEEKNUM(): array
    {
        return require 'tests/data/Calculation/DateTime/WEEKNUM.php';
    }

    /**
     * @dataProvider providerWEEKNUM1904
     *
     * @param mixed $expectedResult
     */
    public function testWEEKNUM1904($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        self::setMac1904();
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=WEEKNUM($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerWEEKNUM1904(): array
    {
        return require 'tests/data/Calculation/DateTime/WEEKNUM1904.php';
    }

    /**
     * @dataProvider providerWeekNumArray
     */
    public function testWeekNumArray(array $expectedResult, string $dateValues, string $methods): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=WEEKNUM({$dateValues}, {$methods})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerWeekNumArray(): array
    {
        return [
            'row vector #1' => [[[1, 25, 29]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '1'],
            'column vector #1' => [[[1], [13], [26]], '{"2023-01-01"; "2023-04-01"; "2023-07-01"}', '1'],
            'matrix #1' => [[[1, 53], [1, 53]], '{"2021-01-01", "2021-12-31"; "2023-01-01", "2023-12-31"}', '1'],
            'row vector #2' => [[[25, 24]], '"2022-06-12"', '{1, 2}'],
            'column vector #2' => [[[13], [14]], '"2023-04-01"', '{1; 2}'],
            'matrix #2' => [[[53, 53], [53, 52]], '"2021-12-31"', '{1, 2; 16, 21}'],
        ];
    }
}
