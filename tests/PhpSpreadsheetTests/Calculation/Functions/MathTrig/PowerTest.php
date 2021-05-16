<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class PowerTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPOWER
     *
     * @param mixed $expectedResult
     * @param mixed $base
     * @param mixed $exponent
     */
    public function testPOWER($expectedResult, $base = 'omitted', $exponent = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($base !== null) {
            $sheet->getCell('A1')->setValue($base);
        }
        if ($exponent !== null) {
            $sheet->getCell('A2')->setValue($exponent);
        }
        if ($base === 'omitted') {
            $sheet->getCell('B1')->setValue('=POWER()');
        } elseif ($exponent === 'omitted') {
            $sheet->getCell('B1')->setValue('=POWER(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=POWER(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPOWER(): array
    {
        return require 'tests/data/Calculation/MathTrig/POWER.php';
    }
}
