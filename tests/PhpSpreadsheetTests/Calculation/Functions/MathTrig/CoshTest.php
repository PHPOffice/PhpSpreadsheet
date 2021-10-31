<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class CoshTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCosh
     *
     * @param mixed $expectedResult
     */
    public function testCosh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=COSH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerCosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/COSH.php';
    }
}
