<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
