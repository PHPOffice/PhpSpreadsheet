<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImSinhTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMSINH
     *
     * @param mixed $expectedResult
     */
    public function testIMSINH($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMSINH', $expectedResult, ...$args);
    }

    public function providerIMSINH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSINH.php';
    }

    /**
     * @dataProvider providerImSinHArray
     */
    public function testImSinHArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSINH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSinHArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.94150493327087-0.92349077604317i', '-0.59847214410396i', '-0.94150493327087-0.92349077604317i'],
                    ['-0.63496391478474-1.298457581416i', '-0.8414709848079i', '0.63496391478474-1.298457581416i'],
                    ['-0.63496391478474+1.298457581416i', '0.8414709848079i', '0.63496391478474+1.298457581416i'],
                    ['0.94150493327087+0.92349077604317i', '0.59847214410396i', '-0.94150493327087+0.92349077604317i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
