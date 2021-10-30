<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SqrtPiTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testSQRTPI($expectedResult, $number): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=SQRTPI()');
        } else {
            $sheet->getCell('B1')->setValue('=SQRTPI(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSQRTPI(): array
    {
        return require 'tests/data/Calculation/MathTrig/SQRTPI.php';
    }
}
