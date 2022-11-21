<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImCosTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCOS
     *
     * @param mixed $expectedResult
     */
    public function testIMCOS($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCOS', $expectedResult, ...$args);
    }

    public function providerIMCOS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOS.php';
    }

    /**
     * @dataProvider providerImCosArray
     */
    public function testImCosArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOS({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCosArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['3.3132901461132-5.0910715229497i', 6.1322894796637, '3.3132901461132+5.0910715229497i'],
                    ['0.83373002513115-0.98889770576287i', 1.5430806348152, '0.83373002513115+0.98889770576287i'],
                    ['0.83373002513115+0.98889770576287i', 1.5430806348152, '0.83373002513115-0.98889770576287i'],
                    ['3.3132901461132+5.0910715229497i', 6.1322894796637, '3.3132901461132-5.0910715229497i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
