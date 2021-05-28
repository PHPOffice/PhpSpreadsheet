<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SqrtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSQRT
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testSQRT($expectedResult, $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=SQRT()');
        } else {
            $sheet->getCell('B1')->setValue('=SQRT(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerSqrt(): array
    {
        return require 'tests/data/Calculation/MathTrig/SQRT.php';
    }
}
