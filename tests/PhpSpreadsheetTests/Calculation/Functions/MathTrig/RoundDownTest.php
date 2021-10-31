<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RoundDownTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRoundDown
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testRoundDown($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=ROUNDDOWN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerRoundDown(): array
    {
        return require 'tests/data/Calculation/MathTrig/ROUNDDOWN.php';
    }
}
