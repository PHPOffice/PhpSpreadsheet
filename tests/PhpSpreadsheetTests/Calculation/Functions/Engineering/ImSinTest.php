<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImSinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMSIN
     *
     * @param mixed $expectedResult
     */
    public function testIMSIN($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMSIN', $expectedResult, ...$args);
    }

    public function providerIMSIN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSIN.php';
    }

    /**
     * @dataProvider providerImSinArray
     */
    public function testImSinArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSIN({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSinArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-5.1601436675797-3.2689394320795i', '-6.0502044810398i', '5.1601436675797-3.2689394320795i'],
                    ['-1.298457581416-0.63496391478474i', '-1.1752011936438i', '1.298457581416-0.63496391478474i'],
                    ['-1.298457581416+0.63496391478474i', '1.1752011936438i', '1.298457581416+0.63496391478474i'],
                    ['-5.1601436675797+3.2689394320795i', '6.0502044810398i', '5.1601436675797+3.2689394320795i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
