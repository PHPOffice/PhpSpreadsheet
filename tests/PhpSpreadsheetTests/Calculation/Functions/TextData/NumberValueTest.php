<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class NumberValueTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNUMBERVALUE
     *
     * @param mixed $expectedResult
     * @param mixed $number
     * @param mixed $decimal
     * @param mixed $group
     */
    public function testNUMBERVALUE($expectedResult, $number = 'omitted', $decimal = 'omitted', $group = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=NUMBERVALUE()');
        } elseif ($decimal === 'omitted') {
            $this->setCell('A1', $number);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1)');
        } elseif ($group === 'omitted') {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimal);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1, A2)');
        } else {
            $this->setCell('A1', $number);
            $this->setCell('A2', $decimal);
            $this->setCell('A3', $group);
            $sheet->getCell('B1')->setValue('=NUMBERVALUE(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerNUMBERVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/NUMBERVALUE.php';
    }
}
