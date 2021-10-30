<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class FactTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFACT
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     */
    public function testFACT($expectedResult, $arg1): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=FACT()');
        } else {
            $sheet->getCell('B1')->setValue('=FACT(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerFACT(): array
    {
        return require 'tests/data/Calculation/MathTrig/FACT.php';
    }

    /**
     * @dataProvider providerFACTGnumeric
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     */
    public function testFACTGnumeric($expectedResult, $arg1): void
    {
        $this->mightHaveException($expectedResult);
        self::setGnumeric();
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=FACT()');
        } else {
            $sheet->getCell('B1')->setValue('=FACT(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerFACTGnumeric(): array
    {
        return require 'tests/data/Calculation/MathTrig/FACTGNUMERIC.php';
    }
}
