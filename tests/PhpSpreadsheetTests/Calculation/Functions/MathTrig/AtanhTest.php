<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AtanhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAtanh
     *
     * @param mixed $expectedResult
     */
    public function testAtanh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.8);
        $sheet->getCell('A1')->setValue("=ATANH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAtanh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATANH.php';
    }
}
