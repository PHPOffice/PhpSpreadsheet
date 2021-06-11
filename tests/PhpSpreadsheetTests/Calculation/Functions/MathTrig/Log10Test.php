<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class Log10Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerLN
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testLN($expectedResult, $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG10()');
        } else {
            $sheet->getCell('B1')->setValue('=LOG10(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerLN(): array
    {
        return require 'tests/data/Calculation/MathTrig/LOG10.php';
    }
}
