<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSin
     *
     * @param mixed $expectedResult
     */
    public function testSin($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=SIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerSin(): array
    {
        return require 'tests/data/Calculation/MathTrig/SIN.php';
    }
}
