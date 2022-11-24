<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImCschTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCSCH
     *
     * @param mixed $expectedResult
     */
    public function testIMCSCH($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCSCH', $expectedResult, ...$args);
    }

    public function providerIMCSCH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSCH.php';
    }

    /**
     * @dataProvider providerImCschArray
     */
    public function testImCschArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCSCH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCschArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.54132290619+0.53096557762117i', '1.6709215455587i', '-0.54132290619+0.53096557762117i'],
                    ['-0.30393100162843+0.62151801717043i', '1.1883951057781i', '0.30393100162843+0.62151801717043i'],
                    ['-0.30393100162843-0.62151801717043i', '-1.1883951057781i', '0.30393100162843-0.62151801717043i'],
                    ['0.54132290619-0.53096557762117i', '-1.6709215455587i', '-0.54132290619-0.53096557762117i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
