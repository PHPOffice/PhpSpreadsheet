<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class ExpTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEXP
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testEXP($expectedResult, $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=EXP()');
        } else {
            $sheet->getCell('B1')->setValue('=EXP(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerEXP(): array
    {
        return require 'tests/data/Calculation/MathTrig/EXP.php';
    }
}
