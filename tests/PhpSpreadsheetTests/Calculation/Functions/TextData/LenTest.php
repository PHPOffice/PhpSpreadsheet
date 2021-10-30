<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class LenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLEN
     *
     * @param mixed $expectedResult
     * @param mixed $str
     */
    public function testLEN($expectedResult, $str = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=LEN()');
        } else {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=LEN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEN(): array
    {
        return require 'tests/data/Calculation/TextData/LEN.php';
    }
}
