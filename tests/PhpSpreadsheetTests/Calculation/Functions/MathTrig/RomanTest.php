<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RomanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testROMAN($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A3', 49);
        $sheet->getCell('A1')->setValue("=ROMAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerROMAN(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROMAN.php';
    }
}
