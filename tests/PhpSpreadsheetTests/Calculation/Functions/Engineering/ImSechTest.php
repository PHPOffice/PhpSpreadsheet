<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImSechTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMSECH
     *
     * @param mixed $expectedResult
     */
    public function testIMSECH($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMSECH', $expectedResult, ...$args);
    }

    public function providerIMSECH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSECH.php';
    }

    /**
     * @dataProvider providerImSecHArray
     */
    public function testImSecHArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSECH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSecHArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.61110856415523-0.3476766607105i', '-1.2482156514688', '-0.61110856415523+0.3476766607105i'],
                    ['0.49833703055519-0.59108384172105i', '1.8508157176809', '0.49833703055519+0.59108384172105i'],
                    ['0.49833703055519+0.59108384172105i', '1.8508157176809', '0.49833703055519-0.59108384172105i'],
                    ['-0.61110856415523+0.3476766607105i', '-1.2482156514688', '-0.61110856415523-0.3476766607105i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
