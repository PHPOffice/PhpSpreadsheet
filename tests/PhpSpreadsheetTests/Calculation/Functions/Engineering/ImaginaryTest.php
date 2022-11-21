<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImaginaryTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     */
    public function testIMAGINARY($expectedResult, ...$args): void
    {
        $this->runTestCase('IMAGINARY', $expectedResult, ...$args);
    }

    public function providerIMAGINARY(): array
    {
        return require 'tests/data/Calculation/Engineering/IMAGINARY.php';
    }

    /**
     * @dataProvider providerImaginaryArray
     */
    public function testImaginaryArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMAGINARY({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImaginaryArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-2.5, -2.5, -2.5],
                    [-1.0, -1.0, -1.0],
                    [1.0, 1.0, 1.0],
                    [2.5, 2.5, 2.5],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
