<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class HourTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     */
    public function testHOUR($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=HOUR($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23 2:23:46');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerHOUR(): array
    {
        return require 'tests/data/Calculation/DateTime/HOUR.php';
    }

    /**
     * @dataProvider providerHourArray
     */
    public function testHourArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=HOUR({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerHourArray(): array
    {
        return [
            'row vector' => [[[1, 13, 19]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[1], [13], [19]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[1, 13], [19, 23]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}
