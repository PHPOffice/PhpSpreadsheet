<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class DollarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDOLLAR
     *
     * @param mixed $expectedResult
     * @param mixed $amount
     * @param mixed $decimals
     */
    public function testDOLLAR($expectedResult, $amount = 'omitted', $decimals = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($amount === 'omitted') {
            $sheet->getCell('B1')->setValue('=DOLLAR()');
        } elseif ($decimals === 'omitted') {
            $this->setCell('A1', $amount);
            $sheet->getCell('B1')->setValue('=DOLLAR(A1)');
        } else {
            $this->setCell('A1', $amount);
            $this->setCell('A2', $decimals);
            $sheet->getCell('B1')->setValue('=DOLLAR(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR(): array
    {
        return require 'tests/data/Calculation/TextData/DOLLAR.php';
    }
}
