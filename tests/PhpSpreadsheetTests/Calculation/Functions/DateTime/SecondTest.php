<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class SecondTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSECOND
     *
     * @param mixed $expectedResult
     */
    public function testSECOND($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=SECOND($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23 2:23:46');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerSECOND(): array
    {
        return require 'tests/data/Calculation/DateTime/SECOND.php';
    }

    /**
     * @dataProvider providerSecondArray
     */
    public function testSecondArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SECOND({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerSecondArray(): array
    {
        return [
            'row vector' => [[[3, 15, 21]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[3], [15], [21]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[3, 15], [21, 59]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}
