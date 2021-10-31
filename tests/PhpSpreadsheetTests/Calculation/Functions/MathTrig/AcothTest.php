<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AcothTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACOTH
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testACOTH($expectedResult, $number): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -10);
        $sheet->getCell('A1')->setValue("=ACOTH($number)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerACOTH(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOTH.php';
    }
}
