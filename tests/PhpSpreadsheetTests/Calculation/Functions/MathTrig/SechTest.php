<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SechTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSECH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testSECH($expectedResult, $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=SECH($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerSECH(): array
    {
        return require 'tests/data/Calculation/MathTrig/SECH.php';
    }
}
