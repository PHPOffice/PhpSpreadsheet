<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AsinhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAsinh
     *
     * @param mixed $expectedResult
     */
    public function testAsinh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ASINH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAsinh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ASINH.php';
    }
}
