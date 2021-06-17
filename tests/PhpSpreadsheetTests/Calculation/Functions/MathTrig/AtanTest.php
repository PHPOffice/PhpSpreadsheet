<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AtanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAtan
     *
     * @param mixed $expectedResult
     */
    public function testAtan($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A1')->setValue("=ATAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAtan(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATAN.php';
    }
}
