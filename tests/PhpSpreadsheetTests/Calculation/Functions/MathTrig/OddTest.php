<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class OddTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerODD
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testODD($expectedResult, $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=ODD($value)");
        $sheet->getCell('A2')->setValue(3.7);
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerODD(): array
    {
        return require 'tests/data/Calculation/MathTrig/ODD.php';
    }
}
