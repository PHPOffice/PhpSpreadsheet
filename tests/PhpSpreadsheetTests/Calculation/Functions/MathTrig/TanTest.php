<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class TanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTan
     *
     * @param mixed $expectedResult
     */
    public function testTan($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1);
        $sheet->getCell('A1')->setValue("=TAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerTan(): array
    {
        return require 'tests/data/Calculation/MathTrig/TAN.php';
    }
}
