<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
