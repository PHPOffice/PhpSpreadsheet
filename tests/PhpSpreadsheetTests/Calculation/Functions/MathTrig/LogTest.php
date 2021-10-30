<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class LogTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOG
     *
     * @param mixed $expectedResult
     * @param mixed $number
     * @param mixed $base
     */
    public function testLOG($expectedResult, $number = 'omitted', $base = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($base !== null) {
            $sheet->getCell('A2')->setValue($base);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG()');
        } elseif ($base === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=LOG(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOG(): array
    {
        return require 'tests/data/Calculation/MathTrig/LOG.php';
    }
}
