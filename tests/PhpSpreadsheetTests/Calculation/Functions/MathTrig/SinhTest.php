<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SinhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCosh
     *
     * @param mixed $expectedResult
     */
    public function testSinh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=SINH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerCosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/SINH.php';
    }
}
