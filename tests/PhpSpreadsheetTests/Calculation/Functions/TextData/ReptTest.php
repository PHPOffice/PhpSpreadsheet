<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ReptTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerREPT
     *
     * @param mixed $expectedResult
     * @param mixed $val
     * @param mixed $rpt
     */
    public function testReptThroughEngine($expectedResult, $val = 'omitted', $rpt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($val === 'omitted') {
            $sheet->getCell('B1')->setValue('=REPT()');
        } elseif ($rpt === 'omitted') {
            $this->setCell('A1', $val);
            $sheet->getCell('B1')->setValue('=REPT(A1)');
        } else {
            $this->setCell('A1', $val);
            $this->setCell('A2', $rpt);
            $sheet->getCell('B1')->setValue('=REPT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPT(): array
    {
        return require 'tests/data/Calculation/TextData/REPT.php';
    }
}
