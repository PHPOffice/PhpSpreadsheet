<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImLnTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     */
    public function testIMLN($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMLN', $expectedResult, ...$args);
    }

    public function providerIMLN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLN.php';
    }

    /**
     * @dataProvider providerImLnArray
     */
    public function testImLnArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLN({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImLnArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.99050073443329-1.9513027039073i', '0.91629073187416-1.5707963267949i', '0.99050073443329-1.1902899496825i'],
                    ['0.34657359027997-2.3561944901923i', '-1.5707963267949i', '0.34657359027997-0.78539816339745i'],
                    ['0.34657359027997+2.3561944901923i', '1.5707963267949i', '0.34657359027997+0.78539816339745i'],
                    ['0.99050073443329+1.9513027039073i', '0.91629073187416+1.5707963267949i', '0.99050073443329+1.1902899496825i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
