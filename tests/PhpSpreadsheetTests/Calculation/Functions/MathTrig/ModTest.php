<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class ModTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMOD
     *
     * @param mixed $expectedResult
     * @param mixed $dividend
     * @param mixed $divisor
     */
    public function testMOD($expectedResult, $dividend = 'omitted', $divisor = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($dividend !== null) {
            $sheet->getCell('A1')->setValue($dividend);
        }
        if ($divisor !== null) {
            $sheet->getCell('A2')->setValue($divisor);
        }
        if ($dividend === 'omitted') {
            $sheet->getCell('B1')->setValue('=MOD()');
        } elseif ($divisor === 'omitted') {
            $sheet->getCell('B1')->setValue('=MOD(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=MOD(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMOD(): array
    {
        return require 'tests/data/Calculation/MathTrig/MOD.php';
    }
}
