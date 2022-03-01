<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DayTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAY
     *
     * @param mixed $expectedResultExcel
     */
    public function testDAY($expectedResultExcel, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResultExcel);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=DAY($dateTimeValue)");
        self::assertSame($expectedResultExcel, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/DAY.php';
    }

    /**
     * @dataProvider providerDAYOpenOffice
     *
     * @param mixed $expectedResultOpenOffice
     */
    public function testDAYOpenOffice($expectedResultOpenOffice, string $dateTimeValue): void
    {
        self::setOpenOffice();
        $this->mightHaveException($expectedResultOpenOffice);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue("=DAY($dateTimeValue)");
        self::assertSame($expectedResultOpenOffice, $sheet->getCell('A2')->getCalculatedValue());
    }

    public function providerDAYOpenOffice(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYOpenOffice.php';
    }

    /**
     * @dataProvider providerDayArray
     */
    public function testDayArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DAY({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerDayArray(): array
    {
        return [
            'row vector' => [[[1, 12, 22]], '{"2022-01-01", "2022-06-12", "2023-07-22"}'],
            'column vector' => [[[1], [3], [6]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}'],
            'matrix' => [[[1, 10], [15, 31]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}'],
        ];
    }
}
