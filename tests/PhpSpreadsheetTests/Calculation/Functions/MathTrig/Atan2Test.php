<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class Atan2Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     */
    public function testATAN2($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A3')->setValue(6);
        $sheet->getCell('A1')->setValue("=ATAN2($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerATAN2(): array
    {
        return require 'tests/data/Calculation/MathTrig/ATAN2.php';
    }
}
