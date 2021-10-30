<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class CleanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCLEAN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testCLEAN($expectedResult, $value = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=CLEAN()');
        } else {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=CLEAN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerCLEAN(): array
    {
        return require 'tests/data/Calculation/TextData/CLEAN.php';
    }
}
