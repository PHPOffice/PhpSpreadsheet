<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

class Days360Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAYS360
     *
     * @param mixed $expectedResult
     */
    public function testDAYS360($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('2000-02-29');
        $sheet->getCell('C1')->setValue('2000-03-31');
        $sheet->getCell('A1')->setValue("=DAYS360($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAYS360(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS360.php';
    }
}
