<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;

class WeekDayTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerWEEKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWEEKDAY($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=WEEKDAY($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerWEEKDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/WEEKDAY.php';
    }

    public function testWEEKDAYwith1904Calendar(): void
    {
        self::setMac1904();
        self::assertEquals(7, Week::day('1904-01-02'));
        self::assertEquals(6, Week::day('1904-01-01'));
        self::assertEquals(6, Week::day(null));
    }

    /**
     * @dataProvider providerWeekDayArray
     */
    public function testWeekDayArray(array $expectedResult, string $dateValues, string $styles): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=WEEKDAY({$dateValues}, {$styles})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerWeekDayArray(): array
    {
        return [
            'row vector #1' => [[[7, 1, 7]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '1'],
            'column vector #1' => [[[1], [7], [7]], '{"2023-01-01"; "2023-04-01"; "2023-07-01"}', '1'],
            'matrix #1' => [[[6, 6], [1, 1]], '{"2021-01-01", "2021-12-31"; "2023-01-01", "2023-12-31"}', '1'],
            'row vector #2' => [[[7, 6]], '"2022-01-01"', '{1, 2}'],
            'column vector #2' => [[[1], [7]], '"2023-01-01"', '{1; 2}'],
        ];
    }
}
