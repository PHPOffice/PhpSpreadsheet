<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
