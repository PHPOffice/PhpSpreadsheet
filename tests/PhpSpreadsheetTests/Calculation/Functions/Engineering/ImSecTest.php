<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImSecTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMSEC
     *
     * @param mixed $expectedResult
     */
    public function testIMSEC($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMSEC', $expectedResult, ...$args);
    }

    public function providerIMSEC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSEC.php';
    }

    /**
     * @dataProvider providerImSecArray
     */
    public function testImSecArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSEC({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSecArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.089798602872122+0.13798100670997i', '0.16307123192998', '0.089798602872122-0.13798100670997i'],
                    ['0.49833703055519+0.59108384172105i', '0.64805427366389', '0.49833703055519-0.59108384172105i'],
                    ['0.49833703055519-0.59108384172105i', '0.64805427366389', '0.49833703055519+0.59108384172105i'],
                    ['0.089798602872122-0.13798100670997i', '0.16307123192998', '0.089798602872122+0.13798100670997i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
