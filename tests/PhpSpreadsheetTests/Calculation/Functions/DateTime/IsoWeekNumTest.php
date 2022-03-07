<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class IsoWeekNumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerISOWEEKNUM
     *
     * @param mixed $expectedResult
     * @param string $dateValue
     */
    public function testISOWEEKNUM($expectedResult, $dateValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=ISOWEEKNUM($dateValue)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerISOWEEKNUM(): array
    {
        return require 'tests/data/Calculation/DateTime/ISOWEEKNUM.php';
    }

    /**
     * @dataProvider providerISOWEEKNUM1904
     *
     * @param mixed $expectedResult
     * @param string $dateValue
     */
    public function testISOWEEKNUM1904($expectedResult, $dateValue): void
    {
        $this->mightHaveException($expectedResult);
        self::setMac1904();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=ISOWEEKNUM($dateValue)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerISOWEEKNUM1904(): array
    {
        return require 'tests/data/Calculation/DateTime/ISOWEEKNUM1904.php';
    }

    /**
     * @dataProvider providerIsoWeekNumArray
     */
    public function testIsoWeekNumArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISOWEEKNUM({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerIsoWeekNumArray(): array
    {
        return [
            'row vector' => [[[52, 23, 29]], '{"2022-01-01", "2022-06-12", "2023-07-22"}'],
            'column vector' => [[[52], [13], [26]], '{"2023-01-01"; "2023-04-01"; "2023-07-01"}'],
            'matrix' => [[[53, 52], [52, 52]], '{"2021-01-01", "2021-12-31"; "2023-01-01", "2023-12-31"}'],
        ];
    }
}
