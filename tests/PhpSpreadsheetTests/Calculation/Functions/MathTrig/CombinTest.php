<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class CombinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOMBIN
     *
     * @param mixed $expectedResult
     * @param mixed $numObjs
     * @param mixed $numInSet
     */
    public function testCOMBIN($expectedResult, $numObjs, $numInSet): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($numObjs !== null) {
            $sheet->getCell('A1')->setValue($numObjs);
        }
        if ($numInSet !== null) {
            $sheet->getCell('A2')->setValue($numInSet);
        }
        $sheet->getCell('B1')->setValue('=COMBIN(A1,A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerCOMBIN(): array
    {
        return require 'tests/data/Calculation/MathTrig/COMBIN.php';
    }
}
