<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
