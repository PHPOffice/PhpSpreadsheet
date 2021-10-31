<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class TanhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTanh
     *
     * @param mixed $expectedResult
     */
    public function testTanh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1);
        $sheet->getCell('A1')->setValue("=TANH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerTanh(): array
    {
        return require 'tests/data/Calculation/MathTrig/TANH.php';
    }
}
