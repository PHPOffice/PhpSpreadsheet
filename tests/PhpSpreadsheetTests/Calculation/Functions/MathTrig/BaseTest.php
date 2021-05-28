<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class BaseTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBASE
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    public function testBASE($expectedResult, $arg1 = 'omitted', $arg2 = 'omitted', $arg3 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        if ($arg3 !== null) {
            $sheet->getCell('A3')->setValue($arg3);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE(A1)');
        } elseif ($arg3 === 'omitted') {
            $sheet->getCell('B1')->setValue('=BASE(A1, A2)');
        } else {
            $sheet->getCell('B1')->setValue('=BASE(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBASE(): array
    {
        return require 'tests/data/Calculation/MathTrig/BASE.php';
    }
}
