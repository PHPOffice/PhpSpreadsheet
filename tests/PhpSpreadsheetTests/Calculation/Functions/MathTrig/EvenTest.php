<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class EvenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testEVEN($expectedResult, $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=EVEN($value)");
        $sheet->getCell('A2')->setValue(3.7);
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerEVEN(): array
    {
        return require 'tests/data/Calculation/MathTrig/EVEN.php';
    }
}
