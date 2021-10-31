<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class TruncTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testTRUNC($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=TRUNC($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTRUNC(): array
    {
        return require 'tests/data/Calculation/MathTrig/TRUNC.php';
    }
}
