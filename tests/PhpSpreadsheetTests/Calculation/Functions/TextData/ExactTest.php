<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ExactTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEXACT
     *
     * @param mixed $expectedResult
     * @param mixed $string1
     * @param mixed $string2
     */
    public function testEXACT($expectedResult, $string1 = 'omitted', $string2 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($string1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=EXACT()');
        } elseif ($string2 === 'omitted') {
            $this->setCell('A1', $string1);
            $sheet->getCell('B1')->setValue('=EXACT(A1)');
        } else {
            $this->setCell('A1', $string1);
            $this->setCell('A2', $string2);
            $sheet->getCell('B1')->setValue('=EXACT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerEXACT(): array
    {
        return require 'tests/data/Calculation/TextData/EXACT.php';
    }
}
