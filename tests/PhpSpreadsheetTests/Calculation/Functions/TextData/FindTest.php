<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class FindTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFIND
     *
     * @param mixed $expectedResult
     * @param mixed $string1
     * @param mixed $string2
     * @param mixed $start
     */
    public function testFIND($expectedResult, $string1 = 'omitted', $string2 = 'omitted', $start = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($string1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=FIND()');
        } elseif ($string2 === 'omitted') {
            $this->setCell('A1', $string1);
            $sheet->getCell('B1')->setValue('=FIND(A1)');
        } elseif ($start === 'omitted') {
            $this->setCell('A1', $string1);
            $this->setCell('A2', $string2);
            $sheet->getCell('B1')->setValue('=FIND(A1, A2)');
        } else {
            $this->setCell('A1', $string1);
            $this->setCell('A2', $string2);
            $this->setCell('A3', $start);
            $sheet->getCell('B1')->setValue('=FIND(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIND(): array
    {
        return require 'tests/data/Calculation/TextData/FIND.php';
    }
}
