<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImConjugateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     */
    public function testIMCONJUGATE($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCONJUGATE', $expectedResult, ...$args);
    }

    public function providerIMCONJUGATE(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCONJUGATE.php';
    }

    /**
     * @dataProvider providerImConjugateArray
     */
    public function testImConjugateArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCONJUGATE({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImConjugateArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-1+2.5i', '2.5i', '1+2.5i'],
                    ['-1+i', 'i', '1+i'],
                    ['-1-i', '-i', '1-i'],
                    ['-1-2.5i', '-2.5i', '1-2.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
