<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RoundUpTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRoundUp
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testRoundUp($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=ROUNDUP($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerRoundUp(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROUNDUP.php';
    }
}
