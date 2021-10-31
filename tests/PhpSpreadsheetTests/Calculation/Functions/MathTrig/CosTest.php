<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class CosTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCos
     *
     * @param mixed $expectedResult
     */
    public function testCos($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=COS($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerCos(): array
    {
        return require 'tests/data/Calculation/MathTrig/COS.php';
    }
}
