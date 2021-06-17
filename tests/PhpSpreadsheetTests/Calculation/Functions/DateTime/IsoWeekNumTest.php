<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
