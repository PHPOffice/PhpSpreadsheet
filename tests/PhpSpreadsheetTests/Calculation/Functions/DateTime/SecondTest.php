<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}
