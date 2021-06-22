<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class TrimTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRIM
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testTRIM($expectedResult, $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=TRIM()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=TRIM(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerTRIM(): array
    {
        return require 'tests/data/Calculation/TextData/TRIM.php';
    }
}
