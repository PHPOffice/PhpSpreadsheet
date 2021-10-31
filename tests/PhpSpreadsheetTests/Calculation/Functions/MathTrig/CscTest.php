<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class CscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCSC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSC($expectedResult, $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CSC($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerCSC(): array
    {
        return require 'tests/data/Calculation/MathTrig/CSC.php';
    }
}
