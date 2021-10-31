<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AsinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAsin
     *
     * @param mixed $expectedResult
     */
    public function testAsin($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ASIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAsin(): array
    {
        return require 'tests/data/Calculation/MathTrig/ASIN.php';
    }
}
