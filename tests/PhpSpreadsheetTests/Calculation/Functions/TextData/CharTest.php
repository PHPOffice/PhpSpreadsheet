<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class CharTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCHAR
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testCHAR($expectedResult, $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CHAR()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CHAR(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHAR(): array
    {
        return require 'tests/data/Calculation/TextData/CHAR.php';
    }
}
