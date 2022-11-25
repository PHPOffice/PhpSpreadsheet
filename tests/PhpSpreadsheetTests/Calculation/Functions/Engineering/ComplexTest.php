<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ComplexTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEX($expectedResult, ...$args): void
    {
        $this->runTestCase('COMPLEX', $expectedResult, ...$args);
    }

    public function providerCOMPLEX(): array
    {
        return require 'tests/data/Calculation/Engineering/COMPLEX.php';
    }

    /**
     * @dataProvider providerComplexArray
     */
    public function testComplexArray(array $expectedResult, string $real, string $imaginary): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMPLEX({$real}, {$imaginary})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerComplexArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-2.5-2.5i', '-1-2.5i', '-2.5i', '1-2.5i', '2.5-2.5i'],
                    ['-2.5-i', '-1-i', '-i', '1-i', '2.5-i'],
                    ['-2.5', '-1', '0.0', '1', '2.5'],
                    ['-2.5+i', '-1+i', 'i', '1+i', '2.5+i'],
                    ['-2.5+2.5i', '-1+2.5i', '2.5i', '1+2.5i', '2.5+2.5i'],
                ],
                '{-2.5, -1, 0, 1, 2.5}',
                '{-2.5; -1; 0; 1; 2.5}',
            ],
        ];
    }
}
