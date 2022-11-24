<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImCotTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCOT
     *
     * @param mixed $expectedResult
     */
    public function testIMCOT($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCOT', $expectedResult, ...$args);
    }

    public function providerIMCOT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOT.php';
    }

    /**
     * @dataProvider providerImCotArray
     */
    public function testImCotArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOT({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCotArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.012184711291981+0.99433328540776i', '1.0135673098126i', '0.012184711291981+0.99433328540776i'],
                    ['-0.2176215618544+0.86801414289593i', '1.3130352854993i', '0.2176215618544+0.86801414289593i'],
                    ['-0.2176215618544-0.86801414289593i', '-1.3130352854993i', '0.2176215618544-0.86801414289593i'],
                    ['-0.012184711291981-0.99433328540776i', '-1.0135673098126i', '0.012184711291981-0.99433328540776i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
