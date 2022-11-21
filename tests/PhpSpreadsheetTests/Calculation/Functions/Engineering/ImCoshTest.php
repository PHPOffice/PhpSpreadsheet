<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImCoshTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMCOSH
     *
     * @param mixed $expectedResult
     */
    public function testIMCOSH($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMCOSH', $expectedResult, ...$args);
    }

    public function providerIMCOSH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOSH.php';
    }

    /**
     * @dataProvider providerImCoshArray
     */
    public function testImCoshArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOSH({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCoshArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-1.2362291988563+0.70332517811353i', -0.80114361554693, '-1.2362291988563-0.70332517811353i'],
                    ['0.83373002513115+0.98889770576287i', 0.54030230586814, '0.83373002513115-0.98889770576287i'],
                    ['0.83373002513115-0.98889770576287i', 0.54030230586814, '0.83373002513115+0.98889770576287i'],
                    ['-1.2362291988563-0.70332517811353i', -0.80114361554693, '-1.2362291988563+0.70332517811353i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
