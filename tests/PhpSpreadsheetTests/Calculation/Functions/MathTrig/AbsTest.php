<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class AbsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAbs
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testRound($expectedResult, $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=ABS()');
        } else {
            $sheet->getCell('B1')->setValue('=ABS(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerAbs(): array
    {
        return require 'tests/data/Calculation/MathTrig/ABS.php';
    }
}
