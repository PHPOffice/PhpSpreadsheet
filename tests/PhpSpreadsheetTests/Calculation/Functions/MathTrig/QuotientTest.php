<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class QuotientTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerQUOTIENT
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function testQUOTIENT($expectedResult, $arg1 = 'omitted', $arg2 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=QUOTIENT()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=QUOTIENT(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=QUOTIENT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerQUOTIENT(): array
    {
        return require 'tests/data/Calculation/MathTrig/QUOTIENT.php';
    }
}
