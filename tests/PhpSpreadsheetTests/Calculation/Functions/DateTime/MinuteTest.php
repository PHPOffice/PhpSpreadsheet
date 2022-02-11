<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class MinuteTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINUTE
     *
     * @param mixed $expectedResult
     */
    public function testMINUTE($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=MINUTE($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23 2:23:46');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerMINUTE(): array
    {
        return require 'tests/data/Calculation/DateTime/MINUTE.php';
    }

    /**
     * @dataProvider providerMinuteArray
     */
    public function testMinuteArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MINUTE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerMinuteArray(): array
    {
        return [
            'row vector' => [[[2, 14, 20]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[2], [14], [20]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[2, 14], [20, 59]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}
