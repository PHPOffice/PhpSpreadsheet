<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

class DateDifTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDATEDIF
     *
     * @param mixed $expectedResult
     */
    public function testDATEDIF($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=DATEDIF($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDATEDIF(): array
    {
        return require 'tests/data/Calculation/DateTime/DATEDIF.php';
    }
}
