<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class ArabicTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerARABIC
     *
     * @param mixed $expectedResult
     * @param string $romanNumeral
     */
    public function testARABIC($expectedResult, $romanNumeral): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue($romanNumeral);
        $sheet->getCell('B1')->setValue('=ARABIC(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerARABIC(): array
    {
        return require 'tests/data/Calculation/MathTrig/ARABIC.php';
    }
}
