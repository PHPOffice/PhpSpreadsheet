<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class DegreesTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDEGREES
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testDegrees($expectedResult, $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=DEGREES()');
        } else {
            $sheet->getCell('B1')->setValue('=DEGREES(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDegrees(): array
    {
        return require 'tests/data/Calculation/MathTrig/DEGREES.php';
    }
}
