<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class FactDoubleTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFACTDOUBLE
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFACTDOUBLE($expectedResult, $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->sheet;
        $sheet->getCell('A1')->setValue($value);
        $sheet->getCell('B1')->setValue('=FACTDOUBLE(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerFACTDOUBLE()
    {
        return require 'tests/data/Calculation/MathTrig/FACTDOUBLE.php';
    }
}
