<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ImLog2Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMLOG2
     *
     * @param mixed $expectedResult
     */
    public function testIMLOG2($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMLOG2', $expectedResult, ...$args);
    }

    public function providerIMLOG2(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLOG2.php';
    }

    /**
     * @dataProvider providerImLog2Array
     */
    public function testImLog2Array(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLOG2({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImLog2Array(): array
    {
        return [
            'row/column vector' => [
                [
                    ['1.4289904975638-2.8151347342002i', '1.3219280948874-2.2661800709136i', '1.4289904975638-1.717225407627i'],
                    ['0.5-3.3992701063704i', '-2.2661800709136i', '0.5-1.1330900354568i'],
                    ['0.5+3.3992701063704i', '2.2661800709136i', '0.5+1.1330900354568i'],
                    ['1.4289904975638+2.8151347342002i', '1.3219280948874+2.2661800709136i', '1.4289904975638+1.717225407627i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
