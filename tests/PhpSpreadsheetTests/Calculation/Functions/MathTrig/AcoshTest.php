<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AcoshTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAcosh
     *
     * @param mixed $expectedResult
     */
    public function testAcosh($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue('1.5');
        $sheet->getCell('A1')->setValue("=ACOSH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAcosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOSH.php';
    }
}
