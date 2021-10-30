<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class CodeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCODE
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testCODE($expectedResult, $character = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CODE()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CODE(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerCODE(): array
    {
        return require 'tests/data/Calculation/TextData/CODE.php';
    }
}
