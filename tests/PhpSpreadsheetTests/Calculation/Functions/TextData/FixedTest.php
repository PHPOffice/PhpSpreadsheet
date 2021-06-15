<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class FixedTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFIXED
     *
     * @param mixed $expectedResult
     * @param mixed $number
     * @param mixed $decimals
     * @param mixed $noCommas
     */
    public function testFIXED($expectedResult, $number = 'omitted', $decimals = 'omitted', $noCommas = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=FIXED()');
        } elseif ($decimals === 'omitted') {
            $this->setCell('A1', $number);
            $sheet->getCell('B1')->setValue('=FIXED(A1)');
        } elseif ($noCommas === 'omitted') {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimals);
            $sheet->getCell('B1')->setValue('=FIXED(A1, A2)');
        } else {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimals);
            $this->setCell('A3', $noCommas);
            $sheet->getCell('B1')->setValue('=FIXED(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIXED(): array
    {
        return require 'tests/data/Calculation/TextData/FIXED.php';
    }
}
