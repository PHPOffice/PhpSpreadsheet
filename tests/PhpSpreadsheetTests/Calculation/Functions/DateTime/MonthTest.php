<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

class MonthTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMONTH
     *
     * @param mixed $expectedResult
     */
    public function testMONTH($expectedResult, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=MONTH($dateTimeValue)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerMONTH(): array
    {
        return require 'tests/data/Calculation/DateTime/MONTH.php';
    }
}
