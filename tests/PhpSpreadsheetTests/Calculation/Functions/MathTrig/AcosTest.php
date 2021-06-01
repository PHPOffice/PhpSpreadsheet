<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AcosTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAcos
     *
     * @param mixed $expectedResult
     */
    public function testAcos($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ACOS($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAcos(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOS.php';
    }
}
